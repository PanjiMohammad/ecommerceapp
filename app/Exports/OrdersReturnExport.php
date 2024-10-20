<?php

namespace App\Exports;

use App\Order;
use App\OrderDetail;
use App\OrderReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersReturnExport implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell
{
    protected $startDate;
    protected $endDate;
    protected $totalSum;

    public function __construct($startDate, $endDate, $totalSum)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalSum = $totalSum;
    }

    /**
     * Fetch the collection of orders with returns for export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $orders = Order::with(['customer', 'return', 'details.product'])
            ->whereHas('return', function ($query) {
                $query->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('seller_id', Auth::guard('seller')->id());
            })
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'DESC')
            ->get();

        if ($orders->isEmpty()) {
            return collect([]);
        }

        // Fetch returns and details based on orders
        $returns = OrderReturn::whereIn('order_id', $orders->pluck('id'))->get();
        $detailOrder = OrderDetail::with(['product'])
            ->whereIn('order_id', $returns->pluck('order_id'))
            ->whereIn('product_id', $returns->pluck('product_id'))
            ->get();

        return $orders->map(function ($order) use ($returns, $detailOrder) {
            $return = $returns->firstWhere('order_id', $order->id);

            return [
                'Tanggal' => optional($return)->created_at ? Carbon::parse($return->created_at)->locale('id')->translatedFormat('l, d F Y') : '',
                'No Faktur' => strtoupper($order->invoice),
                'Pelanggan' => optional($order->customer)->name,
                'Produk' => $detailOrder->where('order_id', $order->id)->map(fn ($detail) => $detail->product->name)->join(', '),
                'Alasan Pengembalian' => optional($return)->reason,
                'Status' => $this->translateStatus(optional($return)->status),
                'Nomor Resi' => $detailOrder->where('order_id', $order->id)->map(fn ($detail) => '#' . strtoupper($detail->tracking_number))->join(', '),
                'Total' => 'Rp ' . number_format(optional($return)->refund_transfer ?? 0, 0, ',', '.'),
            ];
        });
    }

    /**
     * Translate the status value.
     *
     * @param int|null $status
     * @return string
     */
    protected function translateStatus($status)
    {
        switch ($status) {
            case 1:
                return 'Disetujui';
            case 2:
                return 'Ditolak';
            default:
                return '';
        }
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
            'Invoice',
            'Nama Pelanggan',
            'Produk',
            'Alasan Pengembalian',
            'Status',
            'Nomor Resi',
            'Total',
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
        // Date range styling in merged cells B2:C2
        $startDateFormatted = Carbon::parse($this->startDate)->locale('id')->translatedFormat('l, d F Y');
        $endDateFormatted = Carbon::parse($this->endDate)->locale('id')->translatedFormat('l, d F Y');
        $sheet->mergeCells('B2:C2')->setCellValue('B2', 'Periode: ' . $startDateFormatted . ' - ' . $endDateFormatted);

        // Set width for specific columns
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30); // For Grand Total

        // Set default row height for all rows
        $sheet->getDefaultRowDimension()->setRowHeight(25);

        // Date range cell styling
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

        // Header row styling
        $sheet->getStyle('B4:I4')->applyFromArray([
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
                'startColor' => ['argb' => 'FFFFFF00'], // Yellow background
            ],
        ]);

        $highestRow = $sheet->getHighestRow();

        // Data rows styling
        if ($highestRow > 4) {
            $sheet->getStyle('B5:I' . $highestRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
            ]);
        } else {
            // Handling "No Data" case
            $sheet->mergeCells('B5:I5');
            $sheet->setCellValue('B5', 'Tidak Ada Laporan Return');
            $sheet->getStyle('B5:I5')->applyFromArray([
                'font' => ['italic' => true, 'color' => ['argb' => 'FF0000']], // Red color
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
            ]);
        }

        // Set column widths for all columns (optimized loop)
        $columnWidths = [
            'B' => 30, 'C' => 30, 'D' => 30, 'E' => 25, 'F' => 30, 'G' => 20, 'H' => 20, 'I' => 30
        ];
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // Add Grand Total cell below column I
        $grandTotalRow = $highestRow + 1;
        $sheet->setCellValue('H' . $grandTotalRow, 'Total : ');
        $sheet->setCellValue('I' . $grandTotalRow, $this->totalSum); // Assuming $this->totalSum holds the grand total

        // Style the Grand Total row
        $sheet->getStyle('H' . $grandTotalRow . ':I' . $grandTotalRow)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ]);

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
