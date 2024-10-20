<table>
    <thead>
        <tr>
            @foreach ($headings as $heading)
                <th>{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ optional($order->return)->created_at->format('Y-m-d') }}</td>
                <td>{{ $order->invoice }}</td>
                <td>{{ optional($order->customer)->name }}</td>
                <td>{{ optional($order->product)->name }}</td>
                <td>{{ optional($order->return)->reason }}</td>
                <td>{{ optional($order->return)->status }}</td>
                <td>Rp {{ number_format(optional($order->return)->total_refund ?? 0, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>