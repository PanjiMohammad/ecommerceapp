@extends('layouts.seller')

@section('title')
    <title>Detail Pesanan</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Detail Pesanan </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('orders.newIndex') }}">Pesanan</a></li>
                            <li class="breadcrumb-item active">Detail Pesanan</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="row">
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST PRODUCT  -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('orders.newIndex') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                                <span class="float-right">Pesanan : <span class="text-uppercase font-weight-bold">#{{ $order->invoice }}</span></span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h4 class="mb-3">Detail Pelanggan</h4>
                                        <table class="table table-bordered table-hover">
                                            <tr>
                                                <th width="35%">Nama Pelanggan</th>
                                                <td>{{ $order->customer_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nomor Telepon Pelanggan</th>
                                                <td>{{ $order->customer_phone }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email Pelanggan</th>
                                                <td>{{ $order->customer->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Alamat Pelanggan</th>
                                                <td>{{ $order->customer_address . ', Kecamatan ' . $order->customer->district->name . ', Kota ' . $order->customer->district->city->name . ', ' . $order->customer->district->city->province->name . ', ' . $order->customer->district->city->postal_code . ', Indonesia' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        @php
                                            $subtotal = 0;
                                            $serviceCost = $order->service_cost;
                                            $packagingCost = $order->details->groupBy('seller_id')->count() * 1000;
                                            $shippingCost = $order->details->unique('seller_id')->sum('shipping_cost');
                                            $grandTotal = 0;
                                        
                                            foreach ($order->details as $detail) {
                                                // Calculate subtotal for each product
                                                $items = $detail->price * $detail->qty;
                                                $subtotal += $items;

                                                // Calculate tax (10% of subtotal)
                                                // $tax += $itemSubtotal * 0.10;
                                            }

                                            // Calculate grand total (subtotal + tax + shipping cost)
                                            $grandTotal = $subtotal + $packagingCost + $serviceCost + $shippingCost;
                                        @endphp
                                        <h4 class="mb-3">Rincian Total Harga</h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <span>Subtotal {{ '(' . $order->details->count() . ' item)' }}</span>
                                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <span>Biaya Layanan</span>
                                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($serviceCost, 0, ',', '.') }}</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <span>Biaya Kemasan ({{ $order->details->groupBy('seller_id')->count() }} penjual)</span>
                                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($packagingCost, 0, ',', '.') }}</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr style="background-color: #ededed;">
                                                        <td>
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <span class="font-weight-bold">Total Belanja</span>
                                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($subtotal + $serviceCost + $packagingCost, 0, ',', '.') }}</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!--
                                            <div style="border: 1px solid rgba(0, 0, 0, .125); padding: 15px;">
                                            <div class="d-flex align-items-center justify-content-between mb-2" style="border-bottom: 1px solid #ededed; padding-bottom: 15px;">
                                                <span>Subtotal {{ '(' . $order->details->count() . ' item)' }}</span>
                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                                            </div>
                                            {{-- <div class="d-flex align-items-center justify-content-between mb-2" style="border-bottom: 1px solid #ededed; padding-bottom: 15px;">
                                                <span>Ongkos Kirim</span>
                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($shippingCost, 0, ',', '.') }}</span>
                                            </div> --}}
                                            {{-- <div class="d-flex align-items-center justify-content-between mb-2" style="border-bottom: 1px solid #ededed; padding-bottom: 15px;">
                                                <span>PPn 10%</span>
                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($tax, 0, ',', '.') }}</span>
                                            </div> --}}
                                            <div class="d-flex align-items-center justify-content-between mb-2" style="border-bottom: 1px solid #ededed; padding-bottom: 15px;">
                                                <span>Biaya Layanan</span>
                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($serviceCost, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-2" style="border-bottom: 1px solid #ededed; padding-bottom: 15px;">
                                                <span>Biaya Kemasan</span>
                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($packagingCost, 0, ',', '.') }}</span>
                                            </div>
                                            {{-- old --}}
                                            {{-- <div class="d-flex align-items-center justify-content-between">
                                                <span class="font-weight-bold">Total Belanja</span>
                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($grandTotal, 0, ',', '.') }}</span>
                                            </div> --}}

                                            {{-- new --}}
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="font-weight-bold">Total Belanja</span>
                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($subtotal + $serviceCost + $packagingCost, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        -->
                                    </div>
                                </div>
                                <hr>
                                <h4 class="mb-3">Detail Produk</h4>
                                @foreach($details as $index => $row)
                                    <div class="mb-4" style="border: 1px solid rgba(0, 0, 0, .125); padding: 15px;" id="productView_{{ $index }}">
                                        <div class="product-info d-flex align-items-center justify-content-between">
                                            <div>
                                                <p>Status Produk : {!! $row->status_label !!}</p>
                                            </div>

                                            @if ($row->status == 1 && $row->status_payment == 0 && $row->payment['transaction_status'] == 'settlement')
                                                <a href="javascript:void(0);" data-index="{{ $index }}" data-invoice="{{ $order->invoice }}" data-product-id="{{ $row->product_id }}" class="btn btn-success btn-sm approve-payment">Terima Pembayaran</a>
                                            @endif

                                            @if($row->status > 1)
                                                @if($row->status == 2)
                                                    <div class="d-flex justify-content-end">
                                                        <div class="form-group ml-2 mb-0">
                                                            <form id="processForm_{{ $index }}" action="{{ route('orders.newProcess') }}" method="post">
                                                                @csrf
                                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                <input type="hidden" name="product_id" value="{{ $row->product->id }}">
                                                                <button id="submitBtnProcess" class="btn btn-sm btn-secondary" style="margin-left: -2px;" type="submit">Proses Pesanan</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($row->status == 3)
                                                    <div class="d-flex flex-column">
                                                        <div class="d-flex justify-content-end">
                                                            <p class="mt-1 mb-1">Input Resi :</p>
                                                            <div class="form-group ml-2 mb-0">
                                                                <form id="shippingForm_{{ $index }}" action="{{ route('orders.newShipping') }}" method="post">
                                                                    @csrf
                                                                    <div class="input-group">
                                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                        <input type="hidden" name="product_id" value="{{ $row->product->id }}">
                                                                        <input type="text" name="tracking_number" placeholder="Masukkan Resi" class="form-control">
                                                                        <div class="input-group-append">
                                                                            <button type="submit" id="submitBtn" class="btn btn btn-secondary" style="margin-left: -2px;" type="submit">Kirim</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <span style="margin-left: 10rem; margin-top: 3px; font-size: 14px; color: red;">*salin nomor resi tanpa menggunakan '#'</span>
                                                    </div>
                                                @endif
                                            @endif

                                            @if($row->status_return != null)
                                                @if($row->status_return == 0 && $row->status == 5)
                                                    <a href="{{ route('orders.newReturn', ['invoice' => $order->invoice, 'product_id' => $row->product_id]) }}">
                                                        <span class="btn btn-sm btn-outline-danger ml-3">Permintaan Return</span>
                                                    </a>
                                                @endif

                                                @if($row->status_return == 1 && $row->status == 6)
                                                    <button class="btn btn-sm btn-outline-success form-return-success ml-3" data-invoice="{{ $order->invoice }}" data-index="{{ $index }}" data-product-id="{{ $row->product_id }}">Lihat Return Form</button>
                                                @endif
                                            @endif
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h4 class="mb-3">Detail Pembayaran</h4>
                                                {{-- <div class="payment-detail">
                                                    <div class="table-responsive">
                                                        <table class="payment-table">
                                                            <tr>
                                                                <td class="label">Nama Pengirim</td>
                                                                <td class="value">{{ $row->payment['name'] ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="label">Tipe Pembayaran</td>
                                                                <td class="value">{!! ($row->payment['payment_method_name'] ?? '') . ' - ' . ($row->payment['acquirer_name'] ?? '') !!}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="label">Tanggal Pembayaran</td>
                                                                <td class="value">
                                                                    {{ isset($row->payment['transfer_date']) ? \Carbon\Carbon::parse($row->payment['transfer_date'])->locale('id')->translatedFormat('l, d F Y H:i') : '-' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="label">Status Pembayaran</td>
                                                                <td class="value">{!! $row->payment['status_label'] ?? '-' !!}</td>
                                                            </tr>
                                                            @if($row->status_return)
                                                                <tr>
                                                                    <td class="label">Status Return</td>
                                                                    <td class="value">{!! $row->status_label_return ?? '-' !!}</td>
                                                                </tr>
                                                                @if($row->status_return == 1)
                                                                    <tr>
                                                                        <td class="label">Total Refund</td>
                                                                        <td class="value">Rp {{ isset($row->return['refund_transfer']) ? number_format($row->return['refund_transfer'], 0, ',', '.') : '-' }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endif
                                                        </table>
                                                    </div>
                                                </div> --}}
                                                @if ($row->status_payment != 0)
                                                    <div class="payment-detail">
                                                        <div class="table-responsive">
                                                            <table class="payment-table">
                                                                <tr>
                                                                    <td class="label">Nama Pengirim</td>
                                                                    <td class="value">{{ $row->payment['name'] }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label">Tipe Pembayaran</td>
                                                                    <td class="value">{!! ($row->payment['payment_method_name'] ?? '-') . ' - ' . ($row->payment['acquirer_name'] ?? '-') !!}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label">Tanggal Bayar</td>
                                                                    <td class="value">{{ $row->formatted_transfer_date }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="label">Status Pembayaran</td>
                                                                    <td class="value">{!! $row->payment['status_label'] !!}</td>
                                                                </tr>
                                                                @if($row->status_return)
                                                                    <tr>
                                                                        <td class="label">Status Return</td>
                                                                        <td class="value">{!! $row->status_label_return !!}</td>
                                                                    </tr>
                                                                    @if($row->status_return == 1)
                                                                        <tr>
                                                                            <td class="label">Total Refund</td>
                                                                            <td class="value">Rp {{ number_format($row->return['refund_transfer'], 0, ',', '.') }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endif
                                                            </table>
                                                        </div>
                                                    </div>
                                                @else
                                                    <h5 class="text-center">Belum Konfirmasi Pembayaran</h5>
                                                @endif
                                            </div>
                                            <div class="col-md-8">
                                                <h4 class="mb-3">Detail Produk</h4>
                                                <div style="border: 1px solid rgba(0, 0, 0, .125); padding: 15px;">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <p class="mb-2">Id Produk : <span class="font-weight-bold">#{{ $row->product->id }}</span></p>
                                                        @if($row->tracking_number != null)
                                                            <p class="mb-2">Nomor Resi : <span class="font-weight-bold text-uppercase">#{{ $row->tracking_number }}</span></p>
                                                        @endif
                                                    </div>
                                                    {{-- <div class="d-flex justify-content-between">
                                                        <p class="mb-2">Status Detail Produk : <span class="badge badge-secondary">Baru</span></p>
                                                        <p class="mb-2">Status Detail Produk : <span class="badge badge-secondary">Baru</span></p>
                                                    </div> --}}
                                                    <div class="table-responsive">
                                                        <table class="table table-no-bordered table-hover table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-left">Produk</th>
                                                                    <th class="text-left">Harga</th>
                                                                    <th class="text-right">Qty</th>
                                                                    <th class="text-right">Berat</th>
                                                                    <th class="text-right" style="width: 20%;">Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="font-weight-bold">
                                                                    <td class="text-left">
                                                                        <div class="d-flex align-items-center">
                                                                            <div style="display: block; ; width: 80px; height: 80px; border: 1px solid #transparent; flex-shrink: 0;">
                                                                                <img style="object-fit: contain; width: 100%; height: 100%; border-radius: 5px;" src="{{ asset('/products/' . $row->product->image) }}" alt="{{ $row->product->name }}">
                                                                            </div>
                                                                            <span class="ml-2">{{ $row->product->name }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-left" style="vertical-align: middle;">Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                                                                    <td class="text-right" style="vertical-align: middle;">{{ $row->qty . ' item' }}</td>
                                                                    @php
                                                                        $weight = $row->weight;

                                                                        if (strpos($weight, '-') !== false) {
                                                                            // If the weight is a range, split it into an array
                                                                            $weights = explode('-', $weight);
                                                                            $minWeight = (float) trim($weights[0]);
                                                                            $maxWeight = (float) trim($weights[1]);

                                                                            // Check if the weights are >= 1000 to display in Kg
                                                                            $minWeightDisplay = $minWeight >= 1000 ? ($minWeight / 1000) : $minWeight;
                                                                            $maxWeightDisplay = $maxWeight >= 1000 ? ($maxWeight / 1000) . ' Kg' : $maxWeight . ' gram / pack';

                                                                            // Construct the display string
                                                                            $weightDisplay = $minWeightDisplay . ' - ' . $maxWeightDisplay;
                                                                        } else {
                                                                            // Single weight value
                                                                            $weightDisplay = $weight >= 1000 ? ($weight / 1000) . ' Kg' : $weight . ' gram / pack';
                                                                        }
                                                                    @endphp
                                                                    <td class="text-right" style="vertical-align: middle; width: 100px;">{{ $weightDisplay }}</td>
                                                                    <td class="text-right" style="vertical-align: middle; width: 100px;">{{ 'Rp ' . number_format($row->price * $row->qty, 0, ',', '.') }}</td>
                                                                </tr>
                                                                {{-- <tr>
                                                                    <td colspan="2" class="text-left">Subtotal</td>
                                                                    <td colspan="3" class="text-right">Rp {{ number_format($row->price * $row->qty, 0, ',', '.') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-left">Ongkos Kirim (Kurir : {{ $row->shipping_service }})</td>
                                                                    <td colspan="3" class="text-right">Rp {{ number_format($row->shipping_cost, 0, ',', '.') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-left">PPn 10%</td>
                                                                    <td colspan="3" class="text-right">Rp {{ number_format(($row->price * $row->qty) * 0.10, 0, ',', '.') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-left">Total</td>
                                                                    <td colspan="3" class="text-right">Rp {{ number_format(($row->price * $row->qty) + (($row->price * $row->qty) * 0.10) + $row->shipping_cost, 0, ',', '.') }}</td>
                                                                </tr> --}}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- Modal Return Form -->
    <div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%;" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="returnModalLabel">Detail Pengembalian Pesanan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 15px 25px">
                    <div class="mb-4">
                        <h4 class="mb-3">Detail Pelanggan</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <th width="30%">Nama Pelanggan</th>
                                        <td id="customerName"></td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Telepon Pelanggan</th>
                                        <td id="customerPhone"></td>
                                    </tr>
                                    <tr>
                                        <th>Email Pelanggan</th>
                                        <td id="customerEmail"></td>
                                    </tr>
                                    <tr>
                                        <th>Alamat Pelanggan</th>
                                        <td id="customerAddress"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>  
                    <hr>
                    <div class="row">
                        <div class="col-md-7">
                            <h4 class="mb-3">Detail Produk</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <tbody>
                                        <tr>
                                            <th width="50%">Tanggal Return</th>
                                            <td id="returnDate"></td>
                                        </tr>
                                        <tr>
                                            <th width="50%">Nomor Resi</th>
                                            <td id="returnTrackingNumber"></td>
                                        </tr>
                                        <tr>
                                            <th>Nama Produk</th>
                                            <td id="returnProduct"></td>
                                        </tr>
                                        <tr>
                                            <th>Kuantiti</th>
                                            <td id="returnProductQty"></td>
                                        </tr>
                                        <tr>
                                            <th>Alasan Return</th>
                                            <td id="returnReason"></td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Pengembalian Dana</th>
                                            <td id="refundAmount"></td>
                                        </tr>
                                        <tr>
                                            <th>Status Return</th>
                                            <td id="returnStatus"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="product-image text-center">
                                <h4 class="mb-3">Foto Produk</h4>
                                <div class="image-container">
                                    <img src="" id="productPhoto" class="img-fluid" style="display: block; padding: 5px; border-radius: 4px; border: 1px solid #ddd;" alt="Product Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {

            // set timeout for session
            setTimeout(function() {
                $('.alert-success').remove();
                $('.alert-danger').remove();
            }, 2000);

            // form return
            $('.form-return-success, .form-return-reject').on('click', function(e) {
                e.preventDefault();
                var invoice = $(this).data('invoice');
                var index = $(this).data('index');
                var productId = $(this).data('product-id');
                var url = "{{ route('orders.newReturnDetails', ['invoice' => ':invoice', 'product_id' => ':product_id']) }}";
                url = url.replace(':invoice', invoice).replace(':product_id', productId);

                $.ajax({
                    url: url,
                    method: "GET",
                    beforeSend: function() {
                        $('.form-return-success[data-index="' + index + '"]').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i>', 
                            overlayCSS: {
                                backgroundColor: '#fff',
                                opacity: 0.8,
                                cursor: 'wait'
                            },
                            css: {
                                border: 0,
                                padding: 0,
                                backgroundColor: 'none'
                            }
                        });
                    },
                    complete: function() {
                        $('.form-return-success[data-index="' + index + '"]').unblock();
                    },
                    success: function(response) {
                        // Populate modal fields with response data
                        $('#returnProduct').text(response.return_product);
                        $('#returnDate').text(response.return_date);
                        $('#returnProductQty').text(response.product_qty);
                        $('#returnTrackingNumber').text(response.return_tracking_number);
                        $('#customerName').text(response.customer_name);
                        $('#customerPhone').text(response.customer_phone);
                        $('#customerEmail').text(response.customer_email);
                        $('#customerAddress').text(response.customer_address);
                        $('#returnReason').text(response.return.reason);
                        $('#refundAmount').text(response.refund_amount);
                        $('#returnStatus').html(response.status_label);
                        $('#orderId').val(response.order_id);
                        $('#productId').val(response.product_id);
                        $('#productPhoto').attr('src', response.product_photo);

                        // Show the modal
                        $('#returnModal').modal('show');
                    }
                });
            });

            // process order
            @foreach($details as $index => $row)
                $('#processForm_{{ $index }}').on('submit', function(e) {
                    e.preventDefault();
                    var formData = $(this).serialize();

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah Anda yakin ingin memproses pesanan ini?',
                        icon: 'question',
                        showCancelButton: true,
                        cancelButtonColor: '#d33',
                        confirmButtonColor: 'green',
                        cancelButtonText: 'Tidak',
                        confirmButtonText: 'Ya, Proses',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: $(this).attr('action'),
                                type: 'POST',
                                data: formData,
                                beforeSend: function() {
                                    $('#productView_{{ $index }}').block({ 
                                        message: '<i class="fa fa-spinner fa-spin"></i>',
                                        overlayCSS: {
                                            backgroundColor: '#fff',
                                            opacity: 0.8,
                                            cursor: 'wait'
                                        },
                                        css: {
                                            border: 0,
                                            padding: 0,
                                            backgroundColor: 'none'
                                        }
                                    });
                                },
                                success: function(response) {
                                    $('#productView_{{ $index }}').unblock();
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: response.success,
                                            timer: 1500, // Display for 2 seconds
                                            showCancelButton: false,
                                            showConfirmButton: false,
                                            willClose: () => {
                                                location.reload(true);
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.error,
                                            timer: 1500, // Display for 2 seconds
                                            showCancelButton: false,
                                            showConfirmButton: false,
                                            willClose: () => {
                                                window.location.reload();
                                            }
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    $('#productView_{{ $index }}').unblock();
                                    // Handle error
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.error) {
                                        errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: errorMessage,
                                        timer: 1500, // Display for 2 seconds
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            window.location.reload();
                                        }
                                    });
                                },
                            });
                        }
                    });
                });
            @endforeach

            // shipping order
            @foreach($details as $index => $row)
            $('#shippingForm_{{ $index }}').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#productView_{{ $index }}').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i>',
                            overlayCSS: {
                                backgroundColor: '#fff',
                                opacity: 0.8,
                                cursor: 'wait'
                            },
                            css: {
                                border: 0,
                                padding: 0,
                                backgroundColor: 'none'
                            }
                        });
                    },
                    complete: function () {
                        $('#productView_{{ $index }}').unblock();
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.success,
                                timer: 2000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                willClose: () => {
                                    location.reload(true);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.error,
                                timer: 2000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                willClose: () => {
                                    location.reload(true);
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            html: errorMessage,
                            timer: 1500,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                location.reload(true);
                            }
                        });
                    }
                });
            });
            @endforeach
            
            // terima pembayaran
            $('.approve-payment').on('click', function() {
                var invoice = $(this).data('invoice');
                var productId = $(this).data('product-id');
                var button = $(this);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menerima pembayaran ini?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonColor: 'green',
                    cancelButtonText: 'Tidak',
                    confirmButtonText: 'Ya, terima',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("orders.new_approve_payment", ["invoice" => ":invoice", "product_id" => ":product_id"]) }}'
                                .replace(':invoice', invoice)
                                .replace(':product_id', productId),
                            type: 'GET',
                            beforeSend: function() {
                                $('#productView_{{ $index }}').block({ 
                                    message: '<i class="fa fa-spinner fa-spin"></i>',
                                    overlayCSS: {
                                        backgroundColor: '#fff',
                                        opacity: 0.8,
                                        cursor: 'wait'
                                    },
                                    css: {
                                        border: 0,
                                        padding: 0,
                                        backgroundColor: 'none'
                                    }
                                });
                            },
                            success: function(response) {
                                $('#productView_{{ $index }}').unblock();
                                if (response.success === true) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000, // Display for 2 seconds
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            location.reload(true);
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan.',
                                        timer: 2000, // Display for 2 seconds
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            location.reload(true);
                                        }
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                $('#productView_{{ $index }}').unblock();
                                var response = JSON.parse(xhr.responseText);
                                if (response.error) {
                                    errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                }
                                // Handle specific errors if needed
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    timer: 2000, // Display for 2 seconds
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        location.reload(true);
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

@section('css')
    <style>
        .product-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem; /* Optional: adjust space below the row */
        }

        .product-info p {
            margin: 0; /* Optional: adjust spacing between paragraphs */
        }

        .btn {
            margin-left: 1rem; /* Optional: adjust space between text and button */
        }

        .payment-table {
            width: 100%;
            height: 80%;
            border-collapse: collapse;
            border: 1px solid rgba(0, 0, 0, .125);
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: white;
        }

        .payment-table td {
            padding: 10px;
            /* vertical-align: top; */
        }

        .payment-table .label {
            font-weight: bold;
            width: 50%;
        }

        /* .payment-table .separator {
            width: 5%;
        } */

        .payment-table .value {
            /* width: 65%; */
        }

        .payment-table .value a {
            display: inline-block;
            margin-top: 5px;
        }

        .modal-title {
            font-weight: bold;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .img-fluid {
            max-width: 70%;
            height: auto;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        /* /for modal return */

    </style>
@endsection