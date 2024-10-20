<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Order</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <h5>Laporan Order Periode ({{ \Carbon\Carbon::parse($date[0])->locale('id')->translatedFormat('l, d F Y') }} - {{ \Carbon\Carbon::parse($date[1])->locale('id')->translatedFormat('l, d F Y') }})</h5>
    <hr>
    
    <div class="table-responsive">
        <table class="table table-hover table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th style="text-align: left;">Tanggal</th>
                    <th style="text-align: left;">Invoice</th>
                    <th style="text-align: left;">Pelanggan</th>
                    <th style="text-align: left;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                    @forelse ($orders as $row)
                        @php
                            $subtotal = 0;
                            $packagingCost = $row->details->groupBy('seller_id')->count() * 1000;
                            $serviceCost = $row->service_cost;

                            foreach ($row->details as $detail) {
                                $items = ($detail->qty * $detail->price);

                                $productReturn = $row->return->first(function ($return) use ($detail) {
                                    return $return->order_id === $detail->order_id && $return->product_id === $detail->product_id;
                                });

                                if($productReturn){
                                    $subtotal += $items - $productReturn->amount;
                                } else {
                                    $subtotal += $items;
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->locale('id')->translatedFormat('d M Y') }}</td>
                            <td style="text-transform: uppercase; width: 25%;">{{ $row->invoice }}</td>
                            <td>
                                <label><strong>{{ $row->customer_name }}</strong> ({{ $row->customer_phone }})</label><br>
                                <label>{{ $row->customer_address . 'Kecamatan ' . $row->customer->district->name . ', ' . $row->customer->district->city->name . ' ' . $row->customer->district->city->province->name . ', Kode Pos ' . $row->customer->district->city->postal_code }}</label>
                            </td>
                            <td>Rp {{ number_format($subtotal + $packagingCost + $serviceCost, 0, ',', '.') }}</td>
                        </tr>
    
                        @php $total += $row->subtotal + $row->service_cost + $row->packaging_cost @endphp
                    @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot style="margin-top: 30px;">
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: 700;">Total : </td>
                    <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>    
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT6qGFtvhPY5Tyc5p7fPzrF/mV9nYIv3j5dAf5d0e5ynl5A64h" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-q6E9RHvbIyZFJoft+2mJbHaEWldFbQ2hFgycQAmyVmC8yyzW96p/6Y3AoDyk70PI" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"></script>
</body>
</html>