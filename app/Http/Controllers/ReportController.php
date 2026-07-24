<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassAttendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\TeacherSchedule;
use App\Models\TeachingSchedule;
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
        $viewMode   = $request->input('view_mode', 'weekly');
        $reportType = $request->input('report_type', 'daily');
        $search     = $request->input('search', '');
        
        if ($viewMode === 'daily') {
            $startDate = $request->input('start_date', Carbon::today()->toDateString());
            $endDate   = $startDate;
        } elseif ($viewMode === 'weekly') {
            $baseDate  = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfWeek()->toDateString();
            $endDate   = $baseDate->copy()->endOfWeek()->toDateString();
        } else {
            $baseDate  = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfMonth()->toDateString();
            $endDate   = $baseDate->copy()->endOfMonth()->toDateString();
        }

        $calculated = $this->calculateReportData($startDate, $endDate, $reportType, $search);
        $reportData  = $calculated['reportData'];
        $dates       = $calculated['dates'];
        $totalStats  = $calculated['totalStats'];

        $totalAbsensi  = array_sum($totalStats);
        $totalHadir    = $totalStats['hadir'];
        $kehadiranRate = $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100) : 0;

        if ($request->ajax()) {
            return response()->json([
                'html'          => view('reports._table', compact('reportData', 'dates', 'reportType', 'viewMode'))->render(),
                'stats'         => $totalStats,
                'totalAbsensi'  => $totalAbsensi,
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
        $viewMode   = $request->input('view_mode', 'weekly');
        $search     = $request->input('search', '');
        
        if ($viewMode === 'daily') {
            $startDate = $request->input('start_date', Carbon::today()->toDateString());
            $endDate   = $startDate;
        } elseif ($viewMode === 'weekly') {
            $baseDate  = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfWeek()->toDateString();
            $endDate   = $baseDate->copy()->endOfWeek()->toDateString();
        } else {
            $baseDate  = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfMonth()->toDateString();
            $endDate   = $baseDate->copy()->endOfMonth()->toDateString();
        }

        $calculated = $this->calculateReportData($startDate, $endDate, $reportType, $search);
        $reportData = $calculated['reportData'];
        $dates      = $calculated['dates'];
        $totalStats = $calculated['totalStats'];

        $totalAbsensi  = array_sum($totalStats);
        $totalHadir    = $totalStats['hadir'];
        $kehadiranRate = $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100) : 0;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($reportType === 'daily' ? 'Presensi Harian' : 'Presensi Kelas');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Segoe UI')->setSize(10);

        // Enable Gridlines
        $sheet->setShowGridLines(true);

        // ============================================
        // 1. HEADER SECTION (Title & Info)
        // ============================================
        $dateCount = count($dates);
        $totalCols = ($reportType === 'class' ? 4 : 3) + $dateCount + 5;
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

        // Header Rows Configuration
        // Row 1: Logo Row (Height 55)
        $sheet->getRowDimension(1)->setRowHeight(55);
        $sheet->mergeCells("A1:{$lastColLetter}1");

        // Row 2: Title Row (Height 28)
        $sheet->getRowDimension(2)->setRowHeight(28);
        $sheet->mergeCells("A2:{$lastColLetter}2");
        $sheet->setCellValue('A2', 'LAPORAN ' . strtoupper($reportType === 'daily' ? 'PRESENSI HARIAN' : 'PRESENSI KELAS') . ' GURU');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 15,
                'color' => ['argb' => 'FF0F172A'],
                'name'  => 'Segoe UI',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Row 3: Subtitle Row (Height 20)
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->mergeCells("A3:{$lastColLetter}3");
        $sheet->setCellValue('A3', 'SMK ICB CINTA TEKNIKA');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 11,
                'color' => ['argb' => 'FF334155'],
                'name'  => 'Segoe UI',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Row 4: Info Row (Height 20)
        $sheet->getRowDimension(4)->setRowHeight(20);
        $sheet->mergeCells("A4:{$lastColLetter}4");
        $periodText = Carbon::parse($startDate)->format('d M Y') . ' — ' . Carbon::parse($endDate)->format('d M Y');
        $sheet->setCellValue('A4', 'Periode Laporan: ' . $periodText . '   |   Mode Tampilan: ' . ucfirst($viewMode));
        $sheet->getStyle('A4')->applyFromArray([
            'font' => [
                'size'  => 10,
                'italic' => true,
                'color' => ['argb' => 'FF64748B'],
                'name'  => 'Segoe UI',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Row 5: Spacer Row (Height 10)
        $sheet->getRowDimension(5)->setRowHeight(10);

        // Logo Placement Centered in Row 1
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo ICB CT');
            $drawing->setPath($logoPath);
            $drawing->setHeight(48);
            $drawing->setCoordinates('A1');
            
            // Calculate approximate total width of columns in pixels for centering logo
            $approxWidthPx = (6 + 28 + 22 + ($reportType === 'class' ? 18 : 0) + ($dateCount * ($reportType === 'class' ? 10 : 7)) + (5 * 14)) * 7.5;
            $logoWidthPx = 110;
            $offsetX = max(10, (int)(($approxWidthPx - $logoWidthPx) / 2));
            
            $drawing->setOffsetX($offsetX);
            $drawing->setOffsetY(4);
            $drawing->setWorksheet($sheet);
        }

        // ============================================
        // 2. TABLE HEADER
        // ============================================
        $headerRow = 6;
        $sheet->setCellValue('A' . $headerRow, 'No');
        $sheet->setCellValue('B' . $headerRow, 'Nama Guru');
        $sheet->setCellValue('C' . $headerRow, 'Mata Pelajaran');
        if ($reportType === 'class') {
            $sheet->setCellValue('D' . $headerRow, 'Kelas Utama');
        }
        
        $col = $reportType === 'class' ? 5 : 4;
        foreach ($dates as $date) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colLetter . $headerRow, $date->format('d/m'));
            $col++;
        }
        
        $summaryStartCol = $col;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, $reportType === 'class' ? 'Sesi Hadir (H)' : 'Hadir (H)');
        $col++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'Izin (I)');
        $col++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'Sakit (S)');
        $col++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'Alpha (A)');
        $col++;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'Terlambat (T)');
        $lastCol = $col;

        // Header Base Style (Dark Slate)
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol);
        $headerRange   = 'A' . $headerRow . ':' . $lastColLetter . $headerRow;
        
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF334155']],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        // Distinct colors for summary headers
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF059669'); // Emerald
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 1) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2563EB'); // Blue
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 2) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0891B2'); // Cyan
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 3) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDC2626'); // Red
        $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 4) . $headerRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD97706'); // Amber

        // ============================================
        // 3. DATA ROWS
        // ============================================
        $row = $headerRow + 1;
        $no  = 1;

        foreach ($reportData as $data) {
            $teacher = $data['user'];
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $teacher->name);
            $sheet->setCellValue('C' . $row, $data['teacher']?->major_specialty ?? '-');

            if ($reportType === 'class') {
                $firstClass = collect($data['days'])->first(fn($day) => !empty($day['classes']));
                $classroom  = $firstClass && !empty($firstClass['classes']) ? $firstClass['classes'][0]['classroom'] : '-';
                $sheet->setCellValue('D' . $row, $classroom);
            }

            $dateCol = $reportType === 'class' ? 5 : 4;
            foreach ($dates as $date) {
                $dateStr   = $date->toDateString();
                $dayData   = $data['days'][$dateStr] ?? ['code' => '-', 'status' => 'libur'];
                $code      = $dayData['code'];
                $label     = $dayData['label'] ?? '';
                $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $row;

                // Format cell value: for class report show ratios (e.g. H (1/1))
                if ($reportType === 'class' && $code !== '-' && $label && $label !== '-') {
                    $cellValue = "{$code} ({$label})";
                } else {
                    $cellValue = $code;
                }

                $sheet->setCellValue($cellCoord, $cellValue);

                // Highlight status cells cleanly
                $cellStyle = $sheet->getStyle($cellCoord);
                $cellStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                switch ($code) {
                    case 'H':
                        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD1FAE5');
                        $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF065F46'))->setBold(true);
                        break;
                    case 'T':
                        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFEF3C7');
                        $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF92400E'))->setBold(true);
                        break;
                    case 'I':
                        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDBEAFE');
                        $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E40AF'))->setBold(true);
                        break;
                    case 'S':
                        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFCFFAFE');
                        $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF155E75'))->setBold(true);
                        break;
                    case 'A':
                        $cellStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFEE2E2');
                        $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF991B1B'))->setBold(true);
                        break;
                    default:
                        $cellStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF94A3B8'));
                        break;
                }

                $dateCol++;
            }

            // Write Teacher Summaries
            $summary = $data['summary'];
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
            $rowRange = 'A' . $row . ':' . $lastColLetter . $row;
            $sheet->getStyle($rowRange)->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'font'      => ['size' => 10, 'color' => ['argb' => 'FF1E293B'], 'name' => 'Segoe UI'],
            ]);

            // Alignment tweaks
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            if ($reportType === 'class') {
                $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }

            // Summary numbers center
            for ($i = $summaryStartCol; $i <= $lastCol; $i++) {
                $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i) . $row)
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Subtle Zebra Striping
            if ($no % 2 === 0) {
                $sheet->getStyle('A' . $row . ':C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
                if ($reportType === 'class') {
                    $sheet->getStyle('D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
                }
            }

            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // ============================================
        // 4. TOTAL / SUMMARY ROW
        // ============================================
        $totalRow = $row;
        $sheet->setCellValue('A' . $totalRow, '');
        $sheet->setCellValue('B' . $totalRow, 'TOTAL KESELURUHAN');
        $sheet->setCellValue('C' . $totalRow, '');
        if ($reportType === 'class') {
            $sheet->setCellValue('D' . $totalRow, '');
        }

        $dateCol = $reportType === 'class' ? 5 : 4;
        foreach ($dates as $date) {
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($dateCol) . $totalRow, '');
            $dateCol++;
        }

        $startDataRow = $headerRow + 1;
        $endDataRow   = $row - 1;

        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol) . $totalRow, "=SUM(" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol) . "{$startDataRow}:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol) . "{$endDataRow})");
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 1) . $totalRow, "=SUM(" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 1) . "{$startDataRow}:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 1) . "{$endDataRow})");
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 2) . $totalRow, "=SUM(" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 2) . "{$startDataRow}:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 2) . "{$endDataRow})");
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 3) . $totalRow, "=SUM(" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 3) . "{$startDataRow}:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 3) . "{$endDataRow})");
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 4) . $totalRow, "=SUM(" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 4) . "{$startDataRow}:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($summaryStartCol + 4) . "{$endDataRow})");

        $totalRange = 'A' . $totalRow . ':' . $lastColLetter . $totalRow;
        $sheet->getStyle($totalRange)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Segoe UI'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF334155']]],
        ]);
        $sheet->getStyle('B' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension($totalRow)->setRowHeight(26);

        // ============================================
        // 5. COLUMN WIDTHS
        // ============================================
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(22);
        if ($reportType === 'class') {
            $sheet->getColumnDimension('D')->setWidth(18);
        }
        
        $dateStartCol = $reportType === 'class' ? 5 : 4;
        $dateWidth = $reportType === 'class' ? 10 : 7;
        for ($i = $dateStartCol; $i < $summaryStartCol; $i++) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setWidth($dateWidth);
        }
        for ($i = $summaryStartCol; $i <= $lastCol; $i++) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setWidth(14);
        }

        // Freeze Panes
        $freezeCol = $reportType === 'class' ? 'E' : 'D';
        $sheet->freezePane($freezeCol . ($headerRow + 1));
        $sheet->setAutoFilter('A' . $headerRow . ':' . $lastColLetter . ($row - 1));

        // Output Excel
        $filename = 'Laporan_' . ($reportType === 'daily' ? 'Harian' : 'Kelas') . '_' . Carbon::parse($startDate)->format('dmY') . '_' . Carbon::parse($endDate)->format('dmY') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Private helper to calculate synchronized report data
     */
    private function calculateReportData($startDate, $endDate, $reportType, $search = '')
    {
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
                    'user'    => $teacher,
                    'teacher' => $teacher->teacher,
                    'days'    => [],
                    'summary' => ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'T' => 0],
                ];

                foreach ($dates as $date) {
                    $dayOfWeek   = $date->dayOfWeek;
                    $dateStr     = $date->toDateString();
                    $isWeekend   = in_array($dayOfWeek, [0, 6]);
                    $isHoliday   = in_array($dateStr, $holidays);
                    $hasSchedule = TeacherSchedule::where('user_id', $teacher->id)
                        ->where('day_of_week', $dayOfWeek)->where('is_active', true)->exists();

                    if ($isWeekend || $isHoliday || !$hasSchedule) {
                        $teacherData['days'][$dateStr] = ['status' => 'libur', 'code' => '-'];
                        continue;
                    }

                    $attKey     = $teacher->id . '_' . $dateStr;
                    $attendance = $attendances->get($attKey)?->first();

                    if ($attendance) {
                        $code = match($attendance->status) {
                            'Hadir'     => 'H',
                            'Terlambat' => 'T',
                            'Izin'      => 'I',
                            'Sakit'     => 'S',
                            'Alpha'     => 'A',
                            default     => 'A',
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
                    'user'          => $teacher,
                    'teacher'       => $teacher->teacher,
                    'days'          => [],
                    'summary'       => ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'T' => 0],
                    'class_details' => [],
                ];

                foreach ($dates as $date) {
                    $dateStr   = $date->toDateString();
                    $dayOfWeek = $date->dayOfWeek;
                    $isWeekend = in_array($dayOfWeek, [0, 6]);
                    
                    $schedules = TeachingSchedule::where('user_id', $teacher->id)
                        ->where('day_of_week', $dayOfWeek)->where('is_active', true)
                        ->with(['classroom', 'subject'])->orderBy('start_time')->get();

                    if ($isWeekend || $schedules->isEmpty()) {
                        $teacherData['days'][$dateStr] = ['status' => 'libur', 'code' => '-', 'label' => '-', 'classes' => []];
                        continue;
                    }

                    $attendedCount = 0;
                    $lateCount     = 0;
                    $totalClasses  = $schedules->count();
                    $classDetails  = [];

                    foreach ($schedules as $schedule) {
                        $attendance = $classAttendances->first(function($att) use ($teacher, $dateStr, $schedule) {
                            return $att->user_id === $teacher->id 
                                && $att->date->toDateString() === $dateStr
                                && $att->classroom_id === $schedule->classroom_id
                                && $att->period === $schedule->period;
                        });

                        $classInfo = [
                            'classroom' => $schedule->classroom->name ?? '-',
                            'subject'   => $schedule->subject->name ?? '-',
                            'period'    => $schedule->period,
                            'status'    => 'A',
                        ];

                        if ($attendance) {
                            if ($attendance->check_in_time && $attendance->check_out_time) {
                                $checkInDateStr = $attendance->date ? Carbon::parse($attendance->date)->toDateString() : $dateStr;
                                $checkInStr     = Carbon::parse($attendance->check_in_time)->format('H:i:s');
                                $checkOutStr    = Carbon::parse($attendance->check_out_time)->format('H:i:s');
                                $checkIn        = Carbon::parse("{$checkInDateStr} {$checkInStr}");
                                $checkOut       = Carbon::parse("{$checkInDateStr} {$checkOutStr}");
                                $duration       = (int) max(0, round($checkIn->diffInMinutes($checkOut)));
                                
                                if ($duration >= 30) {
                                    if ($attendance->status === 'Hadir') { 
                                        $attendedCount++; 
                                        $classInfo['status'] = 'H'; 
                                    } elseif ($attendance->status === 'Terlambat') { 
                                        $lateCount++; 
                                        $classInfo['status'] = 'T'; 
                                    }
                                } else {
                                    $classInfo['status'] = 'A';
                                }
                            } elseif ($attendance->check_in_time && !$attendance->check_out_time) {
                                $classInfo['status'] = 'NL';
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

        return [
            'teachers'   => $teachers,
            'dates'      => $dates,
            'reportData' => $reportData,
            'totalStats' => $totalStats,
        ];
    }
}
