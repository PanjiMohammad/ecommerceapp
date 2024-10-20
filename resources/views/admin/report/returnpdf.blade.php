<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Return Order</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <h5>Laporan Return Order Periode ({{ \Carbon\Carbon::parse($date[0])->locale('id')->translatedFormat('l, d M Y') }} - {{ \Carbon\Carbon::parse($date[1])->locale('id')->translatedFormat('l, d M Y') }})</h5>
    <hr>
    <table width="100%" class="table-hover table-bordered">
        <thead>
            <tr>
                <th style="text-align: left;">Tanggal</th>
                <th style="text-align: left;">No Faktur</th>
                <th style="text-align: left;">Pelanggan</th>
                <th style="text-align: left;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse ($orders as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->created_at)->locale('id')->translatedFormat('l, d M Y') }}</td>
                    <td style="text-transform: uppercase; width: 25%;">{{ $row->invoice }}</td>
                    <td>
                        <strong>{{ $row->customer_name }}</strong><br>
                        <label><strong>Telp:</strong> {{ $row->customer_phone }}</label><br>
                        <label><strong>Alamat:</strong> {{ $row->customer_address }} {{ $row->customer->district->name }} - {{  $row->customer->district->city->name}}, {{ $row->customer->district->city->province->name }}</label>
                    </td>
                    <td>Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                </tr>

                @php $total += $row->total @endphp
            @empty
            <tr>
                <td colspan="4" style="text-align: center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: 700; margin-top: 30px;">Total : </td>
                <td style="margin-top: 30px;">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>