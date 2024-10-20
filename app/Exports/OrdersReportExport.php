<?php

namespace App\Exports;

use App\Order;
use App\OrderDetail;
use App\OrderReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersReportExport implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell
{
    protected $startDate;
    protected $endDate;
    protected $grandTotal;

    public function __construct($startDate, $endDate, $grandTotal)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->grandTotal = $grandTotal;
    }

    /**
     * Fetch the collection of orders with returns for export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $sellerId = Auth::guard('seller')->user()->id;

        // Fetch orders with related data
        $orders = Order::with([
            'customer',
            'details.product',
            'details' => function($q) use ($sellerId) {
                $q->where('seller_id', $sellerId)
                    ->where('status', 6)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate]);
            }
        ])
        ->whereHas('details', function ($query) use ($sellerId) {
            $query->where('seller_id', $sellerId)
                ->where('status', 6)
                ->whereBetween('created_at', [$this->startDate, $this->endDate]);
        })
        ->whereBetween('created_at', [$this->startDate, $this->endDate])
        ->orderBy('created_at', 'DESC')
        ->get();

        if ($orders->isEmpty()) {
            return collect([]);
        }

        $orderData = [];

        foreach ($orders as $order) {
            // count
            $subtotal = 0;
            foreach($order->details as $detail){
                $productReturn = $order->return->first(function ($return) use ($detail) {
                    return $return->order_id === $detail->order_id && $return->product_id === $detail->product_id;
                });
        
                // Calculate the item total (quantity * price)
                $items = $detail->qty * $detail->price;
        
                // If a product return exists, subtract the 'refund_transfer' amount, otherwise add the item total
                if($productReturn){
                    // Ensure 'refund_transfer' is numeric and handle any potential null values
                    $subtotal += $items - $productReturn->refund_transfer;
                } else {
                    // Add the item total to the subtotal when no return exists
                    $subtotal += $items;
                }
            }

            // Append each order's data to the $orderData array
            $orderData[] = [
                'Tanggal' => optional($order->created_at)->locale('id')->translatedFormat('l, d F Y'),
                'Invoice' => strtoupper($order->invoice),
                'Pelanggan' => optional($order->customer)->name,
                'Total' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            ];
        }

        return collect($orderData);
    }

    /**
     * Translate the status value.
     *
     * @param int|null $status
     * @return string
     */

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
        // Merge B2 and C2 and set the title "Laporan Periode"
        $sheet->mergeCells('B2:C2');
        $startDateFormatted = Carbon::parse($this->startDate)->locale('id')->translatedFormat('l, d F Y');
        $endDateFormatted = Carbon::parse($this->endDate)->locale('id')->translatedFormat('l, d F Y');
        $sheet->setCellValue('B2', 'Laporan Periode : ' . $startDateFormatted . ' - ' . $endDateFormatted);

        // Style for the merged cell (B2:C2)
        $sheet->getStyle('B2:C2')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Apply styles for the headings (B4:E4)
        $sheet->getStyle('B4:E4')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'], // Green background for headings
            ],
        ]);

        // Set the height for all rows
        $sheet->getDefaultRowDimension()->setRowHeight(25);

        // Set column widths for better readability
        $sheet->getColumnDimension('B')->setWidth(35); // Tanggal
        $sheet->getColumnDimension('C')->setWidth(30); // Invoice
        $sheet->getColumnDimension('D')->setWidth(25); // Pelanggan
        $sheet->getColumnDimension('E')->setWidth(30); // Total

        // Get the highest row
        $highestRow = $sheet->getHighestRow();

        // Check if there is no data (i.e., no data rows from B5 onwards)
        if ($highestRow <= 4) {
            // No data present, merge the data cells and display "Tidak ada data laporan"
            $sheet->mergeCells('B5:E5');
            $sheet->setCellValue('B5', 'Tidak ada data laporan');
            
            // Apply centered alignment for the merged cell
            $sheet->getStyle('B5:E5')->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'italic' => true,
                    'size' => 12,
                    'color' => ['argb' => 'FF0000'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
        } else {
            // Apply borders to the entire table (B4:E{last row})
            $sheet->getStyle('B4:E' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // Set both horizontal and vertical alignment to center for all data rows (B5:E{last row})
            $sheet->getStyle('B5:E' . $highestRow)->getAlignment()->applyFromArray([
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]);

            // Calculate total sum for the 'Total' column after cleaning values
            $totalCell = 'E' . ($highestRow + 1);
            $sheet->setCellValue($totalCell, $this->grandTotal);
            $sheet->setCellValue('D' . ($highestRow + 1), 'Total:'); // Label for total sum

            // Apply styles to the total sum row
            $sheet->getStyle('D' . ($highestRow + 1) . ':E' . ($highestRow + 1))->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
        }

        // Save the Excel file with formulas intact, Excel will recalculate upon opening
        $sheet->setSelectedCell('A1'); // Set focus to a non-relevant cell
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
