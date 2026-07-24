<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class AttendanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::query();

        // Date range filter
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        // Teacher filter
        if ($request->filled('teacher_id')) {
            $query->where('user_id', $request->teacher_id);
        }

        // Clone base query BEFORE applying status filter for accurate statistics cards
        $statsBaseQuery = clone $query;

        $stats = [
            'total'     => (clone $statsBaseQuery)->count(),
            'hadir'     => (clone $statsBaseQuery)->whereIn('status', ['Hadir', 'Tepat Waktu'])->count(),
            'terlambat' => (clone $statsBaseQuery)->where('status', 'Terlambat')->count(),
            'alpha'     => (clone $statsBaseQuery)->where('status', 'Alpha')->count(),
            'izin'      => (clone $statsBaseQuery)->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count(),
        ];

        // Apply status filter to data list
        if ($request->filled('status')) {
            if ($request->status === 'Hadir') {
                // "Hadir" filter matches anyone who attended: Hadir, Tepat Waktu, or Terlambat
                $query->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat']);
            } elseif ($request->status === 'Tepat Waktu') {
                $query->whereIn('status', ['Hadir', 'Tepat Waktu']);
            } elseif ($request->status === 'Izin') {
                $query->whereIn('status', ['Izin', 'Sakit', 'Cuti']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Get paginated data with relationships
        $attendances = $query->with('user')
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Transform items consistently
        $items = $attendances->getCollection()->map(function ($att) {
            return [
                'id'         => $att->id,
                'user_id'    => $att->user_id,
                'date'       => $att->date ? Carbon::parse($att->date)->toDateString() : '',
                'check_in'   => $att->check_in ? Carbon::parse($att->check_in)->format('H:i') . ' WIB' : '-',
                'check_out'  => $att->check_out ? Carbon::parse($att->check_out)->format('H:i') . ' WIB' : '-',
                'status'     => $att->status ?? 'Hadir',
                'notes'      => $att->notes ?? '',
                'user'       => $att->user ? [
                    'id'        => $att->user->id,
                    'name'      => $att->user->name,
                    'photo_url' => $att->user->photo_url,
                ] : null,
            ];
        });

        // Get teachers and statuses for filters
        $teachers = User::where('role', 'guru')->orderBy('name')->get(['id', 'name']);
        $statuses = ['Hadir', 'Tepat Waktu', 'Terlambat', 'Izin', 'Alpha'];

        if ($request->ajax() || $request->wantsJson()) {
            $paginationData = $attendances->toArray();
            $paginationData['data'] = $items;
            $paginationData['stats'] = $stats;

            return response()->json($paginationData);
        }

        return view('attendance.history', [
            'attendances'      => $attendances,
            'transformedItems' => $items,
            'stats'            => $stats,
            'teachers'         => $teachers,
            'statuses'         => $statuses,
        ]);
    }

    public function export(Request $request)
    {
        $query = Attendance::with('user')->orderBy('date', 'desc')->orderBy('check_in', 'desc');

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        if ($request->filled('teacher_id')) {
            $query->where('user_id', $request->teacher_id);
        }
        if ($request->filled('status')) {
            if ($request->status === 'Hadir') {
                $query->whereIn('status', ['Hadir', 'Tepat Waktu', 'Terlambat']);
            } elseif ($request->status === 'Tepat Waktu') {
                $query->whereIn('status', ['Hadir', 'Tepat Waktu']);
            } elseif ($request->status === 'Izin') {
                $query->whereIn('status', ['Izin', 'Sakit', 'Cuti']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $attendances = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Presensi');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Segoe UI')->setSize(10);
        $sheet->setShowGridLines(true);

        // Header Banner
        $lastColLetter = 'G';
        $sheet->getRowDimension(1)->setRowHeight(6);
        $sheet->getStyle("A1:{$lastColLetter}1")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF59E0B']],
        ]);

        $sheet->getRowDimension(2)->setRowHeight(40);
        $sheet->getRowDimension(3)->setRowHeight(22);
        
        $sheet->getStyle("A2:{$lastColLetter}3")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
        ]);

        $sheet->mergeCells("A2:{$lastColLetter}2");
        $sheet->setCellValue('A2', 'LAPORAN RIWAYAT PRESENSI GURU');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 16,
                'color' => ['argb' => 'FFFFFFFF'],
                'name'  => 'Segoe UI',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'indent'     => 1,
            ],
        ]);

        $periodText = ($request->filled('start_date') ? Carbon::parse($request->start_date)->format('d/m/Y') : 'Awal') .
                      ' - ' .
                      ($request->filled('end_date') ? Carbon::parse($request->end_date)->format('d/m/Y') : 'Sekarang');
        $sheet->mergeCells("A3:{$lastColLetter}3");
        $sheet->setCellValue('A3', "SMK ICB CINTA TEKNIKA   •   Periode: {$periodText}   •   Total Data: " . count($attendances));
        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'size'  => 9.5,
                'color' => ['argb' => 'FFCBD5E1'],
                'name'  => 'Segoe UI',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'indent'     => 1,
            ],
        ]);

        $sheet->getRowDimension(4)->setRowHeight(12);

        // Table Header
        $headerRow = 5;
        $headers = ['No', 'Tanggal', 'Nama Guru', 'Masuk', 'Keluar', 'Status', 'Keterangan'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($headers as $idx => $header) {
            $sheet->setCellValue($cols[$idx] . $headerRow, $header);
        }

        $headerRange = 'A' . $headerRow . ':G' . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Segoe UI'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF334155']],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        // Data Rows
        $row = $headerRow + 1;
        $no = 1;

        foreach ($attendances as $att) {
            $checkIn = $att->check_in ? Carbon::parse($att->check_in)->format('H:i') . ' WIB' : '-';
            $checkOut = $att->check_out ? Carbon::parse($att->check_out)->format('H:i') . ' WIB' : '-';
            $dateFormatted = Carbon::parse($att->date)->format('d/m/Y');
            $status = $att->status ?? 'Hadir';

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $dateFormatted);
            $sheet->setCellValue('C' . $row, $att->user->name ?? '-');
            $sheet->setCellValue('D' . $row, $checkIn);
            $sheet->setCellValue('E' . $row, $checkOut);
            $sheet->setCellValue('F' . $row, $status);
            $sheet->setCellValue('G' . $row, $att->notes ?? '-');

            // Alignment & Borders
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Status Styling
            $statusStyle = $sheet->getStyle('F' . $row);
            switch ($status) {
                case 'Hadir':
                case 'Tepat Waktu':
                    $statusStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD1FAE5');
                    $statusStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF065F46'))->setBold(true);
                    break;
                case 'Terlambat':
                    $statusStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFEF3C7');
                    $statusStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF92400E'))->setBold(true);
                    break;
                case 'Izin':
                case 'Sakit':
                case 'Cuti':
                    $statusStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDBEAFE');
                    $statusStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E40AF'))->setBold(true);
                    break;
                case 'Alpha':
                    $statusStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFEE2E2');
                    $statusStyle->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF991B1B'))->setBold(true);
                    break;
            }

            $rowRange = 'A' . $row . ':G' . $row;
            $sheet->getStyle($rowRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                'font'    => ['size' => 10, 'name' => 'Segoe UI'],
            ]);

            if ($no % 2 === 0) {
                $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
                $sheet->getStyle('G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // Column Widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(16);
        $sheet->getColumnDimension('G')->setWidth(30);

        $filename = 'Laporan_Riwayat_Presensi_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}