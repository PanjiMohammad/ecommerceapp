<?php

namespace App\Exports;

use App\Order;
use App\OrderDetail;
use App\OrderReturn;
use App\SellerWithdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WithdrawalsFundExport implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell
{
    protected $startDate;
    protected $endDate;
    protected $sellerId;
    protected $grandTotal;

    public function __construct($startDate, $endDate, $sellerId, $grandTotal)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->sellerId = $sellerId;
        $this->grandTotal = $grandTotal;
    }

    /**
     * Fetch the collection of orders with returns for export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $withdrawal = SellerWithdrawal::where('seller_id', $this->sellerId)
            ->where('status', '<>', null)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'DESC')
            ->get();

        if ($withdrawal->isEmpty()) {
            return collect([]);
        }

        $withdrawalData = [];

        foreach ($withdrawal as $row) {
            $withdrawalData[] = [
                'Tanggal' => optional($row->created_at) ? Carbon::parse($row->created_at)->locale('id')->translatedFormat('l, d F Y') : '',
                'Bank' => $row->bankAccount->bank_name_label ?? '-',
                'Status' => ucfirst($row->status),
                'Jumlah Penarikan Dana' => 'Rp ' . number_format(($row->amount ?? 0), 0, ',', '.'),
            ];
        }

        return collect($withdrawalData);
    }

    /**
     * Define the headings for the Excel export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'Bank',
            'Status',
            'Jumlah Penarikan Dana',
        ];
    }

    /**
     * Apply styles to the worksheet.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Merge cells B2 and C2 for the date range
        $sheet->mergeCells('B2:C2');
        $startDateFormatted = Carbon::parse($this->startDate)->locale('id')->translatedFormat('l, d F Y');
        $endDateFormatted = Carbon::parse($this->endDate)->locale('id')->translatedFormat('l, d F Y');
        $sheet->setCellValue('B2', 'Laporan Penarikan Dana : ' . $startDateFormatted . ' - ' . $endDateFormatted);

        // Set width and height for merged cells B2:C2
        $sheet->getColumnDimension('B')->setWidth(30); 
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getRowDimension(2)->setRowHeight(30);

        // Style for merged cells B2:C2
        $sheet->getStyle('B2:C2')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Style the header row
        $sheet->getStyle('B4:E4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFF00'], // Yellow color
            ],
        ]);

        // Apply styles to all data rows
        if ($highestRow > 4) {
            $sheet->getStyle('B5:E' . $highestRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
            ]);
        } else {
            // Style the "Tidak Ada Data" row
            $sheet->mergeCells('B5:E5');
            $sheet->setCellValue('B5', 'Tidak Ada Laporan Penarikan Dana');
            $sheet->getStyle('B5:E5')->applyFromArray([
                'font' => ['italic' => true, 'color' => ['argb' => 'FF0000']], // Red color
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
            ]);
            // Set row height for the merged row
            $sheet->getRowDimension(5)->setRowHeight(25);

            // Adjust row height based on content of the "Produk" column
            for ($row = 5; $row <= $highestRow; $row++) {
                $cellValue = $sheet->getCell('E' . $row)->getValue();
                $lineCount = substr_count($cellValue, "\n") + 1;
                $rowHeight = 15 * $lineCount; // Approximate height adjustment
                $sheet->getRowDimension($row)->setRowHeight($rowHeight);
            }
        }

        // Calculate total sum for the 'Total' column after cleaning values
        $totalCell = 'E' . ($highestRow + 1);
        $sheet->setCellValue($totalCell, $this->grandTotal);
        $sheet->setCellValue('D' . ($highestRow + 1), 'Total :'); // Label for total sum

        // Apply styles to the total sum row
        $sheet->getStyle('D' . ($highestRow + 1) . ':E' . ($highestRow + 1))->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Adjust row height for the total row
        $sheet->getRowDimension((int) $totalCell)->setRowHeight(25);

        // Set width for each column
        $columns = range('B', 'E');
        foreach ($columns as $column) {
            if ($column == 'B') {
                $sheet->getColumnDimension($column)->setWidth(30);
            } elseif ($column == 'C') {
                $sheet->getColumnDimension($column)->setWidth(30);
            } elseif ($column == 'D') {
                $sheet->getColumnDimension($column)->setWidth(30);
            } else {
                $sheet->getColumnDimension($column)->setWidth(30);
            }
        }

        // Set row height
        for ($row = 3; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(25);
        }

        return [];
    }

    /**
     * Define the starting cell.
     *
     * @return string
     */
    public function startCell(): string
    {
        return 'B4';
    }
}
