<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassAttendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\TeacherSchedule;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $viewMode = $request->input('view_mode', 'weekly');
        $reportType = $request->input('report_type', 'daily');
        $search = $request->input('search', '');
        
        if ($viewMode === 'daily') {
            $startDate = $request->input('start_date', Carbon::today()->toDateString());
            $endDate = $startDate;
        } elseif ($viewMode === 'weekly') {
            $baseDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfWeek()->toDateString();
            $endDate = $baseDate->copy()->endOfWeek()->toDateString();
        } else {
            $baseDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfMonth()->toDateString();
            $endDate = $baseDate->copy()->endOfMonth()->toDateString();
        }

        $teachersQuery = User::where('role', 'guru')->where('is_active', true);
        if ($search) {
            $teachersQuery->where('name', 'like', "%{$search}%");
        }
        $teachers = $teachersQuery->with('teacher')->orderBy('name')->get();

        $dates = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dates[] = $date->copy();
        }

        $totalStats = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
        $reportData = [];

        if ($reportType === 'daily') {
            $attendances = Attendance::whereBetween('date', [$startDate, $endDate])
                ->get()->groupBy(fn($att) => $att->user_id . '_' . $att->date);

            $leaves = LeaveRequest::where('status', 'approved')
                ->where('end_date', '>=', $startDate)->where('start_date', '<=', $endDate)->get();

            $holidays = Holiday::whereBetween('date', [$startDate, $endDate])->pluck('date')->toArray();

            foreach ($teachers as $teacher) {
                $teacherData = [
                    'user' => $teacher, 'teacher' => $teacher->teacher,
                    'days' => [], 'summary' => ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'T' => 0],
                ];

                foreach ($dates as $date) {
                    $dayOfWeek = $date->dayOfWeek;
                    $dateStr = $date->toDateString();
                    $isWeekend = in_array($dayOfWeek, [0, 6]);
                    $isHoliday = in_array($dateStr, $holidays);
                    $hasSchedule = TeacherSchedule::where('user_id', $teacher->id)
                        ->where('day_of_week', $dayOfWeek)->where('is_active', true)->exists();

                    if ($isWeekend || $isHoliday || !$hasSchedule) {
                        $teacherData['days'][$dateStr] = ['status' => 'libur', 'code' => '-'];
                        continue;
                    }

                    $attKey = $teacher->id . '_' . $dateStr;
                    $attendance = $attendances->get($attKey)?->first();

                    if ($attendance) {
                        $code = match($attendance->status) {
                            'Hadir' => 'H', 'Terlambat' => 'T', 'Izin' => 'I', 'Sakit' => 'S', 'Alpha' => 'A', default => 'A',
                        };
                        $teacherData['days'][$dateStr] = ['status' => strtolower($attendance->status), 'code' => $code];
                        $teacherData['summary'][$code]++;
                        $statusKey = strtolower($attendance->status);
                        if (isset($totalStats[$statusKey])) $totalStats[$statusKey]++;
                    } else {
                        $onLeave = $leaves->first(fn($l) => $l->user_id === $teacher->id 
                            && $l->start_date->toDateString() <= $dateStr && $l->end_date->toDateString() >= $dateStr);

                        if ($onLeave) {
                            $code = $onLeave->type === 'sakit' ? 'S' : 'I';
                            $teacherData['days'][$dateStr] = ['status' => $onLeave->type, 'code' => $code];
                            $teacherData['summary'][$code]++;
                            $totalStats[$onLeave->type === 'sakit' ? 'sakit' : 'izin']++;
                        } else {
                            $teacherData['days'][$dateStr] = ['status' => 'alpha', 'code' => 'A'];
                            $teacherData['summary']['A']++;
                            $totalStats['alpha']++;
                        }
                    }
                }

                $reportData[] = $teacherData;
            }
        } else {
            // Presensi Kelas
            $classAttendances = ClassAttendance::with(['classroom', 'teachingSchedule.subject'])
                ->whereBetween('date', [$startDate, $endDate])->get();

            foreach ($teachers as $teacher) {
                $teacherData = [
                    'user' => $teacher, 'teacher' => $teacher->teacher,
                    'days' => [], 'summary' => ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'T' => 0],
                    'class_details' => [],
                ];

                foreach ($dates as $date) {
                    $dateStr = $date->toDateString();
                    $dayOfWeek = $date->dayOfWeek;
                    $isWeekend = in_array($dayOfWeek, [0, 6]);
                    
                    $schedules = \App\Models\TeachingSchedule::where('user_id', $teacher->id)
                        ->where('day_of_week', $dayOfWeek)->where('is_active', true)
                        ->with(['classroom', 'subject'])->orderBy('start_time')->get();

                    if ($isWeekend || $schedules->isEmpty()) {
                        $teacherData['days'][$dateStr] = ['status' => 'libur', 'code' => '-', 'label' => '-', 'classes' => []];
                        continue;
                    }

                    $attendedCount = 0;
                    $lateCount = 0;
                    $totalClasses = $schedules->count();
                    $classDetails = [];

                    foreach ($schedules as $schedule) {
                        $attendance = $classAttendances->first(function($att) use ($teacher, $dateStr, $schedule) {
                            return $att->user_id === $teacher->id 
                                && $att->date->toDateString() === $dateStr
                                && $att->classroom_id === $schedule->classroom_id
                                && $att->period === $schedule->period;
                        });

                        $classInfo = [
                            'classroom' => $schedule->classroom->name ?? '-',
                            'subject' => $schedule->subject->name ?? '-',
                            'period' => $schedule->period,
                            'status' => 'A', // Default Alpha
                        ];

                        if ($attendance) {
                            // SMART MODE: Hanya hitung kalau IN + OUT lengkap
                            if ($attendance->check_in_time && $attendance->check_out_time) {
                                $duration = $attendance->check_in_time->diffInMinutes($attendance->check_out_time);
                                
                                // Harus minimal 30 menit durasi
                                if ($duration >= 30) {
                                    if ($attendance->status === 'Hadir') { 
                                        $attendedCount++; 
                                        $classInfo['status'] = 'H'; 
                                    } elseif ($attendance->status === 'Terlambat') { 
                                        $lateCount++; 
                                        $classInfo['status'] = 'T'; 
                                    }
                                } else {
                                    $classInfo['status'] = 'A'; // Durasi terlalu singkat = Alpha
                                }
                            } elseif ($attendance->check_in_time && !$attendance->check_out_time) {
                                $classInfo['status'] = 'NL'; // Tidak Lengkap (belum scan keluar)
                            }
                        }

                        $classDetails[] = $classInfo;
                    }

                    if ($attendedCount === $totalClasses) {
                        $teacherData['days'][$dateStr] = ['status' => 'hadir', 'code' => 'H', 'label' => "{$attendedCount}/{$totalClasses}", 'classes' => $classDetails];
                        $teacherData['summary']['H']++;
                        $totalStats['hadir']++;
                    } elseif ($attendedCount > 0) {
                        $teacherData['days'][$dateStr] = ['status' => 'terlambat', 'code' => 'T', 'label' => "{$attendedCount}/{$totalClasses}", 'classes' => $classDetails];
                        $teacherData['summary']['T']++;
                        $totalStats['terlambat']++;
                    } else {
                        $teacherData['days'][$dateStr] = ['status' => 'alpha', 'code' => 'A', 'label' => "0/{$totalClasses}", 'classes' => $classDetails];
                        $teacherData['summary']['A']++;
                        $totalStats['alpha']++;
                    }
                }

                $reportData[] = $teacherData;
            }
        }

        $totalAbsensi = array_sum($totalStats);
        $totalHadir = $totalStats['hadir'];
        $kehadiranRate = $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100) : 0;

        if ($request->ajax()) {
            return response()->json([
                'html' => view('reports._table', compact('reportData', 'dates', 'reportType', 'viewMode'))->render(),
                'stats' => $totalStats,
                'totalAbsensi' => $totalAbsensi,
                'kehadiranRate' => $kehadiranRate,
            ]);
        }

        return view('reports.index', compact(
            'reportData', 'dates', 'totalStats', 'totalAbsensi', 'kehadiranRate',
            'startDate', 'endDate', 'viewMode', 'search', 'reportType'
        ));
    }

    public function export(Request $request)
    {
        $reportType = $request->input('report_type', 'daily');
        $viewMode = $request->input('view_mode', 'weekly');
        $search = $request->input('search', '');
        
        if ($viewMode === 'daily') {
            $startDate = $request->input('start_date', Carbon::today()->toDateString());
            $endDate = $startDate;
        } elseif ($viewMode === 'weekly') {
            $baseDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfWeek()->toDateString();
            $endDate = $baseDate->copy()->endOfWeek()->toDateString();
        } else {
            $baseDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfMonth()->toDateString();
            $endDate = $baseDate->copy()->endOfMonth()->toDateString();
        }

        $teachersQuery = User::where('role', 'guru')->where('is_active', true);
        if ($search) $teachersQuery->where('name', 'like', "%{$search}%");
        $teachers = $teachersQuery->with('teacher')->orderBy('name')->get();

        $dates = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) $dates[] = $date->copy();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($reportType === 'daily' ? 'Presensi Harian' : 'Presensi Kelas');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Segoe UI')->setSize(10);

        // ============================================
        // 1. HEADER SECTION (Logo + Title)
        // ============================================
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo ICB CT');
            $drawing->setPath($logoPath);
            $drawing->setHeight(40);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        // Title
        $sheet->setCellValue('B1', 'LAPORAN ' . strtoupper($reportType === 'daily' ? 'PRESENSI HARIAN' : 'PRESENSI KELAS') . ' GURU');
        $sheet->mergeCells('B1:F1');
        $sheet->getStyle('B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['argb' => 'FF0F172A'],
                'name' => 'Segoe UI',
            ],
        ]);
        $sheet->getStyle('B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Subtitle
        $sheet->setCellValue('B2', 'ICB CINTA TEKNIKA');
        $sheet->getStyle('B2')->applyFromArray([
            'font' => [
                'size' => 11,
                'color' => ['argb' => 'FF64748B'],
            ],
        ]);

        // Periode
        $periodText = Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
        $sheet->setCellValue('B3', 'Periode Laporan: ' . $periodText);
        $sheet->getStyle('B3')->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['argb' => 'FF94A3B8'],
            ],
        ]);

        // Row heights
        $sheet->getRowDimension(1)->setRowHeight(45);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(18);
        $sheet->getRowDimension(4)->setRowHeight(10);

        // ============================================
        // 2. TABLE HEADER
        // ============================================
        $headerRow = 5;
        $sheet->setCellValue('A' . $headerRow, 'No');
        $sheet->setCellValue('B' . $headerRow, 'Nama Guru');
        $sheet->setCellValue('C' . $headerRow, 'Mata Pelajaran');
        if ($reportType === 'class') $sheet->setCellValue('D' . $headerRow, 'Kelas');
        
        $col = $reportType === 'class' ? 5 : 4;
        foreach ($dates as $date) {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, $date->format('d/m'));
            $col++;
        }
        $summaryStartCol = $col;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'Hadir');
        $col++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'Izin');
        $col++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'Sakit');
        $col++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'Alpha');
        $col++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'Terlambat');
        $lastCol = $col;

        // Header style (Navy background, white text)
        $headerRange = 'A' . $headerRow . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol) . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF0F172A']],
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF0F172A']],
                'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF0F172A']],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF0F172A']],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(30);

        // Summary header colors
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF10B981'); // Green
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 1) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF3B82F6'); // Blue
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 2) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF06B6D4'); // Cyan
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 3) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEF4444'); // Red
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 4) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF59E0B'); // Yellow

        // ============================================
        // 3. DATA ROWS
        // ============================================
        $row = $headerRow + 1;
        $no = 1;
        foreach ($teachers as $teacher) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $teacher->name);
            $sheet->setCellValue('C' . $row, $teacher->teacher?->major_specialty ?? '-');
            if ($reportType === 'class') $sheet->setCellValue('D' . $row, '-');

            $summary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'T' => 0];
            $dateCol = $reportType === 'class' ? 5 : 4;
            
            foreach ($dates as $date) {
                $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $row, '-');
                $dateCol++;
            }

            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $row, $summary['H']);
            $dateCol++;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $row, $summary['I']);
            $dateCol++;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $row, $summary['S']);
            $dateCol++;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $row, $summary['A']);
            $dateCol++;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $row, $summary['T']);

            // Row style
            $rowRange = 'A' . $row . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol) . $row;
            $sheet->getStyle($rowRange)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'font' => ['size' => 10, 'color' => ['argb' => 'FF1E293B'], 'name' => 'Segoe UI'],
            ]);

            // Left align for name and subject
            $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            if ($reportType === 'class') {
                $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }

            // Zebra striping (subtle)
            if ($row % 2 === 0) {
                $sheet->getStyle($rowRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            // Bold name
            $sheet->getStyle('B' . $row)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FF0F172A'],
                ],
            ]);
            $row++;
        }

        // ============================================
        // 4. SUMMARY ROW (Total)
        // ============================================
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, '');
        $sheet->setCellValue('B' . $totalRow, 'TOTAL');
        $sheet->setCellValue('C' . $totalRow, '');
        if ($reportType === 'class') $sheet->setCellValue('D' . $totalRow, '');

        $dateCol = $reportType === 'class' ? 5 : 4;
        foreach ($dates as $date) {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $totalRow, '');
            $dateCol++;
        }

        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $totalRow, '=SUM(' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol) . ($headerRow + 1) . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol) . ($row - 1) . ')');
        $dateCol++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $totalRow, '=SUM(' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 1) . ($headerRow + 1) . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 1) . ($row - 1) . ')');
        $dateCol++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $totalRow, '=SUM(' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 2) . ($headerRow + 1) . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 2) . ($row - 1) . ')');
        $dateCol++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $totalRow, '=SUM(' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 3) . ($headerRow + 1) . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 3) . ($row - 1) . ')');
        $dateCol++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $totalRow, '=SUM(' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 4) . ($headerRow + 1) . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 4) . ($row - 1) . ')');

        // Total row style
        $totalRange = 'A' . $totalRow . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol) . $totalRow;
        $sheet->getStyle($totalRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF0F172A']]],
        ]);
        $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension($totalRow)->setRowHeight(28);

        // ============================================
        // 5. COLUMN WIDTHS
        // ============================================
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        if ($reportType === 'class') $sheet->getColumnDimension('D')->setWidth(15);
        
        $dateStartCol = $reportType === 'class' ? 5 : 4;
        for ($i = $dateStartCol; $i < $summaryStartCol; $i++) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setWidth(10);
        }
        for ($i = $summaryStartCol; $i <= $lastCol; $i++) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setWidth(14);
        }

        // ============================================
        // 6. FREEZE PANES & AUTO FILTER
        // ============================================
        $sheet->freezePane('A' . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol) . ($row - 1));

        // ============================================
        // 7. OUTPUT
        // ============================================
        $filename = 'Laporan_' . ($reportType === 'daily' ? 'Harian' : 'Kelas') . '_' . Carbon::parse($startDate)->format('dmY') . '_' . Carbon::parse($endDate)->format('dmY') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
