@extends('layouts.ecommerce')

@section('title')
    <title>Order {{ $order->invoice }} - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Detail Pesanan #<span class="text-uppercase">{{ $order->invoice }}</span></h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboad</a>
                        <a href="{{ route('customer.orders') }}">Pesanan</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
	<section class="login_box_area p_100">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
                </div>
				<div class="col-md-9">
                    @if (session('success'))
                        <input type="hidden" id="success-message" value="{{ session('success') }}">
                    @endif

                    @if (session('error'))
                        <input type="hidden" id="error-message" value="{{ session('error') }}">
                    @endif
                    <div class="row">
						<div class="col-md-12">
							<div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mt-3">Data Pelanggan</h4>
                                        @if($details->contains('status', 0))
                                            <a href="{{ route('customer.paymentForm', $order->invoice) }}" class="float-right btn btn-sm btn-success">Bayar Pesanan</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body loader-area-first">
                                    <div class="table-responsive">
                                        <table>
                                            <tr>
                                                <td width="30%"><p class="custom-margin">Invoice</p></td>
                                                <td width="5%"><p class="custom-margin">:</p></td>
                                                <td>
                                                    <p class="custom-margin">
                                                        <a title="Download Invoice" href="{{ route('customer.order_pdf', $order->invoice) }}" title="{{ $order->invoice  }}" target="_blank" class="font-weight-bold text-uppercase order-pdf-link">
                                                            {{ $order->invoice }}
                                                        </a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><p class="custom-margin">Nama Penerima</p></td>
                                                <td width="5%"><p class="custom-margin">:</p></td>
                                                <td><p class="custom-margin">{{ $order->customer_name . ' (' . $order->customer_phone . ')' }}</p></td>
                                            </tr>
                                            <tr>
                                                <td width="30%"><p class="custom-margin">Email Penerima</p></td>
                                                <td width="5%"><p class="custom-margin">:</p></td>
                                                <td><p class="custom-margin">{{ $order->customer->email }}</p></td>
                                            </tr>
                                            <tr>
                                                <td><p class="custom-margin">Alamat Penerima</p></td>
                                                <td><p class="custom-margin">:</p></td>
                                                <td>
                                                    <p class="custom-margin text-justify">{{ $order->customer_address . ', Kecamatan ' . $order->customer->district->name }}, {{ 'Kota ' . $order->customer->district->city->name }}, {{ $order->customer->district->city->province->name . ', ' . $order->customer->district->city->postal_code . ', Indonesia' }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
						</div>
                        <div class="col-md-12 mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title mt-3">Detail Total Belanja</h4>
                                        </div>
                                        <div class="card-body">
                                            <table>
                                                <tr>
                                                    <td width="66%"><p class="custom-margin">Subtotal {{ '(' . $order->details->count() . ' item)' }}</td>
                                                    <td width="7%"><p class="custom-margin">:</p></td>
                                                    <td><p class="custom-margin">{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</p></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="custom-margin">Ongkos Kirim</p></td>
                                                    <td><p class="custom-margin">:</p></td>
                                                    <td><p class="custom-margin">{{ 'Rp ' . number_format($order->cost, 0, ',', '.') }}</p></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="custom-margin">Biaya Layanan</p></td>
                                                    <td><p class="custom-margin">:</p></td>
                                                    <td><p class="custom-margin">{{ 'Rp ' . number_format($order->service_cost, 0, ',', '.') }}</p></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="custom-margin">Biaya Kemasan {{ '(' . $order->details->groupBy('seller_id')->count() . ' penjual)' }}</p></td>
                                                    <td><p class="custom-margin">:</p></td>
                                                    <td><p class="custom-margin">{{ 'Rp ' . number_format($order->packaging_cost, 0, ',', '.') }}</p></td>
                                                </tr>
                                                <tr>
                                                    <td><p class="custom-margin font-weight-bold">Total Belanja</p></td>
                                                    <td><p class="custom-margin font-weight-bold">:</p></td>
                                                    <td><p class="custom-margin font-weight-bold">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</p></td>
                                                </tr>
                                            </table>
                                            {{-- <div class="d-flex justify-content-between align-items-center mb-2" style="border-bottom: 1px solid #ededed; padding-bottom: 8px;">
                                                <span>Subtotal {{ '(' . $order->details->count() . ' item)' }}</span>
                                                <span>{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2" style="border-bottom: 1px solid #ededed; padding-bottom: 8px;">
                                                <span>Ongkos Kirim</span>
                                                <span>{{ 'Rp ' . number_format($order->cost, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2" style="border-bottom: 1px solid #ededed; padding-bottom: 8px;">
                                                <span>Biaya Layanan</span>
                                                <span>{{ 'Rp ' . number_format($order->service_cost, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2" style="border-bottom: 1px solid #ededed; padding-bottom: 8px;">
                                                <span>Biaya Kemasan {{ '(' . $order->details->groupBy('seller_id')->count() . ' penjual)' }}</span>
                                                <span>{{ 'Rp ' . number_format($order->packaging_cost, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="font-weight-bold">Total Belanja</span>
                                                <span class="font-weight-bold">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</span>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title mt-3">Detail Pembayaran</h4>
                                        </div>
                                        <div class="card-body">
                                            @if ($order->payment)
                                                <table>
                                                    <tr>
                                                        <td width="39%"><p class="custom-margin">Nama Pengirim</td>
                                                        <td width="3%"><p class="custom-margin">:</p></td>
                                                        <td><p class="custom-margin">{{ $order->payment->name }}</p></td>
                                                    </tr>
                                                    <tr>
                                                        <td><p class="custom-margin">Tanggal Transfer</p></td>
                                                        <td><p class="custom-margin">:</p></td>
                                                        <td><p class="custom-margin">{{ \Carbon\Carbon::parse($order->payment->transfer_date)->locale('id')->translatedFormat('l, d F Y H:i') }}</p></td>
                                                    </tr>
                                                    <tr>
                                                        <td><p class="custom-margin">Jumlah Transfer</p></td>
                                                        <td><p class="custom-margin">:</p></td>
                                                        <td><p class="custom-margin">{{ 'Rp ' . number_format($order->payment->amount, 0, ',', '.') }}</p></td>
                                                    </tr>
                                                    <tr>
                                                        <td><p class="custom-margin">Metode Pembayaran</p></td>
                                                        <td><p class="custom-margin">:</p></td>
                                                        <td><p class="custom-margin">{!! ($order->payment['payment_method_name'] ?? '-') . ' - ' . ($order->payment['acquirer_name'] ?? '-') !!}</p></td>
                                                    </tr>
                                                </table>
                                            @else
                                                <h4 class="text-center">Belum ada data pembayaran</h4>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            @php $i = 1; @endphp
                            @foreach ($details as $index => $row)
                                <div id="detailView" class="mb-4" style="border: 1px solid rgba(0, 0, 0, .125); border-radius: 5px; padding: 20px 25px 25px 25px;">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            @if($row->status_return != null)
                                                <span><span class="font-weight-bold">Status Produk (Return) :</span> {!! $row->status_label_return !!}</span><br>
                                            @else
                                                <span><span class="font-weight-bold">Status Produk :</span> {!! $row->status_label !!}</span><br>
                                            @endif
                                        </div>
                                        @if($row->tracking_number !== null)
                                            <div>
                                                <span class="font-weight-bold">Nomor Resi :</span>
                                                <span class="font-weight-bold text-uppercase">{{ '#' . $row->tracking_number }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <hr>
                                    <div class="order-container">
                                        @if (isset($row['product']['image']) && isset($row['product']['name']))
                                            <a class="order-image-link" title="Lihat Gambar {{ $row['product']['image'] }}" href="{{ asset('/products/' . $row['product']['image']) }}" target="_blank">
                                                <img src="{{ asset('/products/' . $row['product']['image']) }}" alt="{{ $row['product']['name'] }}" class="order-image">
                                            </a>
                                        @endif
                                        <div class="order-details">
                                            <div class="product-info">
                                                @if (isset($row['product']['name']))
                                                    <p class="product-name ml-3 font-weight-bold">{{ $row['product']['name'] }}</p>
                                                @endif
                                                <p class="product-price font-weight-bold">{{ $row['qty'] . ' x Rp ' . number_format($row['price'], 0, ',', '.') }}</p>
                                            </div>
                                            <p class="product-status ml-3 mt-3">
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
                                                <span>{{ $weightDisplay }}</span>
                                                {{-- @if($row->status_return != null)
                                                    <span>Status : {!! $row->status_label_return !!}</span><br>
                                                @else
                                                    <span>Status : {!! $row->status_label !!}</span><br>
                                                @endif --}}
                                                {{-- <span>Status :
                                                    @if($row->status == 2)
                                                        <span class="badge badge-info">Proses</span>
                                                    @else
                                                        {!! $row->status_label !!}
                                                    @endif
                                                </span> --}}
                                            </p>
                                            <p class="product-service ml-3">
                                                Layanan : {{ $row->shipping_service }}
                                            </p>
                                            <div class="product-links ml-3">
                                                <a href="{{ url('/product/' . $row->product->slug) }}" style="color: #777;" class="view-product btn btn-sm">Lihat Produk</a>
                                                <a href="{{ url('/category/' . $row->product->category->slug) }}" style="color: #777;" class="view-similar btn btn-sm ml-1">Lihat Produk Serupa</a>
                                                <a href="#" class="view-track btn btn-sm ml-1 lacak-produk" data-index="{{ $i }}" style="color: #777;">Lacak Produk</a>
                                            </div>
                                        </div> 
                                    </div>

                                    <div id="detail-pesanan{{ $i }}" class="detail-pesanan mb-4 mt-4" style="display: none; padding: 30px 30px 5px 30px; border: 1px solid #ddd; border-radius: 5px; background-color: #fff;">
                                        <ul class="progress-container">
                                            <li class="step {{ $row->status >= 3  ? 'active' : '' }}" data-step="1">
                                                <div class="icon"><i class="fas fa-box"></i></div>
                                                <div class="line">
                                                    <div class="circle"><i class="fas fa-check"></i></div>
                                                </div>
                                            </li>
                                            <li class="step {{ $row->status >= 4 ? 'active' : '' }}" data-step="2">
                                                <div class="icon"><i class="fas fa-truck"></i></div>
                                                <div class="line">
                                                    <div class="circle"><i class="fas fa-check"></i></div>
                                                </div>
                                            </li>
                                            <li class="step {{ $row->status >= 5 ? 'active' : '' }}" data-step="3">
                                                <div class="icon"><i class="fas fa-handshake"></i></div>
                                                <div class="line">
                                                    <div class="circle"><i class="fas fa-check"></i></div>
                                                </div>
                                            </li>
                                            <li class="step {{ $row->status >= 6 ? 'active' : '' }}" data-step="4">
                                                <div class="icon"><i class="fas fa-archive"></i></div>
                                                <div class="line">
                                                    <div class="circle"><i class="fas fa-check"></i></div>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="detail-pesanan-info mt-5 mb-5">
                                            {{-- <p style="font-size: 20px; color: green;" class="text-center">Pesanan Selesai</p> --}}
                                            @if($row->status == 6 && $row->status_return == 2)
                                                <p style="font-size: 20px; color: red;" class="text-center">Pesanan Selesai & Return Ditolak Oleh Penjual</p>
                                            @elseif($row->status == 6 && $row->status_return == 1)
                                                <p style="font-size: 20px; color: green;" class="text-center">Pesanan Selesai & Return Disetujui Oleh Penjual</p>
                                            @elseif($row->status == 6 && $row->status_return == null)
                                                <p style="font-size: 20px; color: green;" class="text-center">Pesanan Selesai</p>
                                            @elseif($row->status >= 5)
                                                @if($row->status_return != null)
                                                    <p style="font-size: 20px; color: red;" class="text-center">Menunggu konfirmasi return dari penjual</p>
                                                @else
                                                    <p style="font-size: 20px; color: green;" class="text-center">Pesanan Sampai</p>
                                                @endif
                                            @elseif ($row->status == 4)
                                                <p style="font-size: 20px; color: green;" class="text-center">Pesanan Sedang Dikirim</p>
                                            @elseif ($row->status == 3 || $row->status == 2)
                                                <p style="font-size: 20px; color: color: green;" class="text-center">Pembayaran Sudah Dikonfirmasi & Pesanan dalam proses pengemasan</p>
                                            @elseif ($row->status == 1)
                                                <p style="font-size: 20px; color: green;" class="text-center">Menunggu konfirmasi dari Penjual</p>
                                            @else
                                                <p style="font-size: 20px; color: #777;" class="text-center">Pesan Anda Belum Diproses</p>
                                            @endif
                                        </div>
                                        <div class="summary-order-product">
                                            <h4>Status Pemesanan</h4>
                                            <hr>
                                            <ul class="timeline">
                                                @if($row->status >= 6 && $row->status_return == 1)
                                                    <li class="timeline-item {{ $row->status >= 6 && $row->status_return == 1 ? 'active' : '' }}" data-step="6">
                                                        <div class="timeline-content">
                                                            <div class="d-flex justify-content-between">
                                                                <p>System-Automatic - {{ $row->formatted_receive_date_day }}<br> Return disetujui & diterima oleh penjual.</span></p>
                                                                <p>{{ $row->formatted_receive_date }} WIB</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                                @if($row->status >= 6 && $row->status_return == 2)
                                                    <li class="timeline-item {{ $row->status >= 6 && $row->status_return == 2 ? 'active' : '' }}" data-step="6">
                                                        <div class="timeline-content">
                                                            <div class="d-flex justify-content-between">
                                                                <p>System-Automatic - {{ $row->formatted_receive_date_day }}<br> Return ditolak & pesanan dikembalikan ke penerima <span class="text-capitalize">{{ $row->payment['name'] }}</span></p>
                                                                <p>{{ $row->formatted_receive_date }} WIB</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                                @if($row->status >= 6 && $row->status_return == null)
                                                    <li class="timeline-item {{ $row->status >= 6 && $row->status_return == null ? 'active' : '' }}" data-step="6">
                                                        <div class="timeline-content">
                                                            <div class="d-flex justify-content-between">
                                                                <p>System-Automatic - {{ $row->formatted_receive_date_day }}<br> Diterima oleh <span class="text-capitalize">{{ $row->payment['name'] }}</span></p>
                                                                <p>{{ $row->formatted_receive_date }} WIB</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                                {{-- @if($row->status >= 5 && $row->return)
                                                <li class="timeline-item {{ $row->status >= 5 && $row->status_return == 0 ? 'active' : '' }}" data-step="5">
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between">
                                                            <p style="color: #777777">User - {{ $row->formatted_arrived_date_day }} <br> Sedang mengajukan return pada pesanan ini.</p>
                                                            <p>{{ $row->formatted_arrived_date }} WIB</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endif --}}
                                                @if($row->status >= 5)
                                                    @if($row->return)
                                                        <li class="timeline-item {{ $row->status >= 5 && $row->status_return >= 0 ? 'active' : '' }}" data-step="5">
                                                            <div class="timeline-content">
                                                                <div class="d-flex justify-content-between">
                                                                    <p><span style="color: #777777">User - {{ $row->formatted_arrived_date_day }}</span> <br> <span class="text-danger">Sedang mengajukan return pada pesanan ini.</span></p>
                                                                    <p>{{ $row->formatted_created_return }} WIB</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endif
                                                    <li class="timeline-item {{ $row->status >= 5 ? 'active' : '' }}" data-step="5">
                                                        <div class="timeline-content">
                                                            <div class="d-flex justify-content-between">
                                                                <p style="color: #777777">System-Automatic - {{ $row->formatted_arrived_date_day }} <br> Pesanan tiba.</p>
                                                                <p>{{ $row->formatted_arrived_date }} WIB</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                                @if($row->status >= 4)
                                                    <li class="timeline-item {{ $row->status >= 4 ? 'active' : '' }}" data-step="4">
                                                        <div class="timeline-content">
                                                            <div class="d-flex justify-content-between">
                                                                <p>System-Automatic - {{ $row->formatted_shippin_date_day }} <br> Pesanan sedang dikirim.</p>
                                                                <p>{{ $row->formatted_shippin_date }} WIB</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                                @if($row->status >= 3)
                                                    <li class="timeline-item {{ $row->status >= 3 ? 'active' : '' }}" data-step="3">
                                                        <div class="timeline-content">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="text-justify">Seller - {{ $row->formatted_process_date_day }}<br>Pesanan sedang dalam proses pengemasan.</p>
                                                                <p>{{ $row->formatted_process_date }} WIB</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                                @if($row->status >= 2)
                                                    <li class="timeline-item {{ $row->status >= 2 ? 'active' : '' }}" data-step="2">
                                                        <div class="timeline-content">
                                                            <div class="d-flex justify-content-between">
                                                                <p>Seller - {{ $row->formatted_confirm_payment_date_day }}<br>Pembayaran kamu sudah dikonfirmasi.</p>
                                                                <p>{{ $row->formatted_confirm_payment_date }} WIB</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                                @if($row->status >= 1)
                                                    <li class="timeline-item {{ $row->status >= 1 ? 'active' : '' }}" data-step="1">
                                                        <div class="timeline-content">
                                                            <div class="d-flex justify-content-between">
                                                                <p class="text-justify">System-Automatic - {{ $row->formatted_shop_date }}<br>Menunggu Penjual Mengkonfirmasi Pembayaran Kamu.</p>
                                                                <p>{{ $row->formatted_shop_date_time }} WIB</p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    {{-- <hr>
                                    <div class="products-payment-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="font-weight-bold">Metode Pembayaran</span>
                                            <a href="javascript:void(0);" data-index="{{ $i }}" class="lihat-pembayaran font-weight-bold">Lihat pembayaran</a>
                                        </div>
                                        <div class="product-detail-info mt-1" id="product-detail-info{{ $i }}">
                                            <div class="info-container">
                                                <div class="info-text">
                                                    <table class="info-table">
                                                        <tr>
                                                            <td class="label-column">Metode Pembayaran</td>
                                                            <td class="label-column">:</td>
                                                            <td class="value-column">{!! ($row->payment['payment_method_name'] ?? '-') . ' - ' . ($row->payment['acquirer_name'] ?? '-') !!}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="label-column">Tanggal Pembayaran</td>
                                                            <td class="label-column">:</td>
                                                            <td class="value-column">{{ $row->formatted_transfer_date ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="label-column">Jumlah Pembayaran</td>
                                                            <td class="label-column">:</td>
                                                            <td class="value-column">Rp {{ number_format($row->payment['amount'] ?? 0, 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="label-column">Tujuan Pembayaran</td>
                                                            <td class="label-column">:</td>
                                                            <td class="value-column">{{ $row->payment['transfer_to'] ?? '-' }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="info-image">
                                                    @if (!empty($row->payment['proof']))
                                                        <a title="Lihat Bukti Pembayaran {{ $row->payment['proof'] }}" href="{{ asset('/proof/payment/' . $row->payment['proof']) }}" target="_blank">
                                                            <img src="{{ asset('/proof/payment/' . $row->payment['proof']) }}" class="payment-proof" alt="Payment Proof">
                                                        </a>
                                                    @else
                                                    <img style="height: 100px; width: 100px;" src="{{asset('/proof/payment/no-payment.jpeg')}}" alt="Image"/>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    {{-- <hr>
                                    <div class="product-price-info">
                                        <p style="font-size: 20px;">Rincian Harga</p>
                                        <div class="d-flex justify-content-between">
                                            <p>{{ 'Subtotal (' . $row->qty . ' item)' }}</p>
                                            <p>{{ 'Rp ' . number_format($row->price * $row->qty, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <p>Ongkos Kirim</p>
                                            <p>{{ 'Rp ' . number_format($row->shipping_cost, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <p>PPn (10%)</p>
                                            <p>{{ 'Rp ' . number_format($row->price * $row->qty * 0.10, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <p class="font-weight-bold">Total</p>
                                            <p class="font-weight-bold">{{ 'Rp ' . number_format($row->price * $row->qty + $row->price * $row->qty * 0.10 + $row->shipping_cost, 0, ',', '.') }}</p>
                                        </div>
                                    </div>     --}}
                                    <hr>
                                    <div class="justify-content-between align-items-center">
                                        @if($row->status_return == 0 && $row->status_return != null)
                                            <p class="text-danger float-left">*Note : Produk ini sedang mengajukan return.</p>
                                        @endif
                                        
                                        <div class="float-right">
                                            @if($row->status != 4)
                                                @if($row->status == 5 && $row->status_return == null)
                                                    <form id="orderAcceptForm" class="form-inline" method="post">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $row->product_id }}">
                                                                {{-- <a href="" class="btn btn-sm btn-outline-success mr-1">Terima <i class="fas fa-check"></i></a>
                                                                <a href="" class="btn btn-sm btn-outline-danger">Return <i class="fas fa-xmark"></i></a> --}}
                                                        <a href="{{ route('customer.order_return', ['invoice' => $order->invoice, 'product_id' => $row->product_id]) }}" class="btn btn-danger btn-sm" title="Return Pesanan {{ $row->product->name }}">Ajukan Pengembalian <i class="fas fa-xmark ml-1"></i></a>
                                                        <button type="submit" class="btn btn-success btn-sm ml-2" title="Terima Pesanan {{ $row->product->name }}">Terima Pesanan<i class="fas fa-check ml-1"></i></button>
                                                    </form>
                                                @endif
                                            @endif

                                            @if($row->status == 6)
                                                <form class="orderRatingForm" method="POST" id="ratingForm-{{ $row->product_id }}">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $row->product_id }}">
                                                    <input type="hidden" name="order_id" value="{{ $row->order_id }}"> <!-- Add order ID -->
                                                    
                                                    @php
                                                        // Check if user has already rated this product in the specific order
                                                        $existingRating = $row->product->ratings->where('order_id', $row->order_id)->first();
                                                    @endphp

                                                    @if($existingRating)
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="mr-2 font-weight-bold">Beri Rating: </span>
                                                            <div class="rating">
                                                                @for($x = 5; $x >= 1; $x--)
                                                                    <input 
                                                                        type="radio" 
                                                                        id="star{{ $x }}-{{ $row->product_id }}" 
                                                                        name="rating" 
                                                                        value="{{ $x }}"
                                                                        {{ $existingRating->rating == $x ? 'checked' : '' }} 
                                                                        {{ $existingRating ? 'disabled' : '' }}
                                                                    />
                                                                    <label for="star{{ $x }}-{{ $row->product_id }}" title="{{ $x }} Bintang">&#9733;</label>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="mr-2 font-weight-bold">Beri Rating: </span>
                                                            <div class="rating">
                                                                @for($x = 5; $x >= 1; $x--)
                                                                    <input 
                                                                        type="radio" 
                                                                        id="star{{ $x }}-{{ $row->product_id }}" 
                                                                        name="rating" 
                                                                        value="{{ $x }}" 
                                                                    />
                                                                    <label for="star{{ $x }}-{{ $row->product_id }}" title="{{ $x }} Bintang">&#9733;</label>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    @endif
                                                </form>
                                                
                                                <!-- Modal for Comments -->
                                                <div class="modal fade" id="commentModal-{{ $row->product_id }}" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="commentModalLabel">Tulis Komentar untuk Produk {{ $row->product->name }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form class="commentForm" id="commentForm-{{ $row->product_id }}">
                                                                    <div class="form-group">
                                                                        <label for="comment-{{ $row->product_id }}">Komentar:</label>
                                                                        <textarea 
                                                                            name="comment" 
                                                                            id="comment-{{ $row->product_id }}" 
                                                                            class="form-control" 
                                                                            rows="3" 
                                                                            placeholder="Tulis komentar Anda di sini"></textarea>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                                                                <button type="button" class="btn btn-success btn-sm submit-comment" data-product-id="{{ $row->product_id }}">Kirim Komentar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                         
                                            @endif

                                            {{-- @if($row->status_return == 1 && $row->status == 6)
                                                <p>Status Pengembalian Produk : {!! $row->status_label_return !!}</p>
                                            @endif

                                            @if($row->status_return == 2 && $row->status == 6)
                                                <p>Status Pengembalian Produk : {!! $row->status_label_return !!}</p>   
                                            @endif --}}

                                            {{-- @if($row->status == 0)
                                                <a href="{{ route('customer.paymentForm', ['invoice' => $order->invoice, 'product_id' => $row->product_id]) }}" class="btn btn-sm btn-primary float-right">Bayar Pesanan</a>
                                            @endif --}}
                                        </div>
                                        <br>
                                    </div>
                                </div>
                                @php $i++; @endphp
                            @endforeach   
                        </div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            // session
            var successMessage = $('#success-message').val();
            var errorMessage = $('#error-message').val();

            if (successMessage) {
				$.toast({
					heading: 'Berhasil',
					text: successMessage,
					showHideTransition: 'slide',
					icon: 'success',
					position: 'top-right',
					hideAfter: 3000
				});
            }

            if (errorMessage) {
				$.toast({
					heading: 'Gagal',
					text: errorMessage,
					showHideTransition: 'fade',
					icon: 'error',
					position: 'top-right',
					hideAfter: 3000
				});
            }

            // show product payment info
            $('.lihat-pembayaran').on('click', function(e) {
                e.preventDefault();
                var index = $(this).data('index');
                $('#product-detail-info' + index).toggle();
            });

            // download pdf
            $('.order-pdf-link').on('click', function(event){
                event.preventDefault();

                var url = $(this).attr('href');

                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function() {
                        $('.loader-area-first').block({ 
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
                        $('.loader-area-first').unblock();
                    },
                    success: function(response){
                        if (response.success) {
                            $.toast({
                                heading: 'Berhasil',
                                text: response.success,
                                showHideTransition: 'slide',
                                icon: 'success',
                                position: 'top-right',
                                hideAfter: 3000
                            });
                            setTimeout(function() {
                                window.open(url, '_blank');
                            }, 1500);
                        } else {
                            $.toast({
                                heading: 'Gagal',
                                text: 'Terjadi Kesalahan, Silahkann Coba Lagi.',
                                showHideTransition: 'fade',
                                icon: 'error',
                                position: 'top-right',
                                hideAfter: 3000
                            });
                        }
                    },
                    error: function(xhr) {
                        $.toast({
                            heading: 'Gagal',
                            text: 'Terjadi Kesalahan, Silahkann Coba Lagi.',
                            showHideTransition: 'fade',
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                    }
                });
            });

            $('.lacak-produk').click(function(e) {
                e.preventDefault();
                var index = $(this).data('index');
                $('#detail-pesanan' + index).toggle();
            });
            
            // Update the progress bar and timeline based on the current status
            var currentStatus = {!! $row->status !!};

            // Highlight the timeline items starting from status 3
            // $(".timeline-item").each(function() {
            //     var step = $(this).data("step");
            //     if (step <= currentStatus) {
            //         $(this).addClass("active");
            //     }
            // });

            $('input[name="rating"]').change(function() {
                var productId = $(this).closest('form').find('input[name="product_id"]').val();
                $('#commentModal-' + productId).modal('show');
            });

            $(document).on('click', '.submit-comment', function() {
                var productId = $(this).data('product-id');
                var form = $('#ratingForm-' + productId);
                var comment = $('#comment-' + productId).val();
                var rating = form.find('input[name="rating"]:checked').val();

                $.ajax({
                    url: '{{ route("customer.postRating") }}',
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        product_id: form.find('input[name="product_id"]').val(),
                        order_id: form.find('input[name="order_id"]').val(),
                        rating: rating,
                        comment: comment
                    },
                    beforeSend: function() {
                        $('div[id^="detailView"]').block({ 
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
                        $('div[id^="detailView"]').unblock();
                    },
                    success: function(response) {
                        $.toast({  
                            heading: 'Berhasil',
                            text: response.success,
                            showHideTransition: 'fade',
                            icon: 'success',
                            position: 'top-right',
                            hideAfter: 3000,
                        });
                        $('#commentModal-' + productId).modal('hide'); // Close the modal
                        setTimeout(function() {
                            window.location.reload(true);
                        }, 1500);
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON || {};
                        var errorMessage = response.error;

                        $.toast({  
                            heading: 'Error',
                            text: errorMessage,
                            showHideTransition: 'fade',
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                    }
                });
            });

            $('form[id^="orderAcceptForm"]').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = form.serialize();

                bootbox.confirm({
					message: '<i class="fa-sharp fa-solid fa-truck text-success mr-1"></i> Terima pesanan ini ?',
					backdrop: true,
					buttons: {
						confirm: {
							label: 'Ya, terima <i class="fas fa-check ml-1"></i>',
							className: 'btn-success btn-sm'
						},
						cancel: {
							label: 'Tidak <i class="fas fa-xmark ml-1"></i>',
							className: 'btn-danger btn-sm'
						}
					},
					callback: function(result) {
						if(result) {
                            $.ajax({
                                url: '{{ route("customer.order_accept") }}',
                                type: 'POST',
                                data: formData,
                                beforeSend: function() {
                                    $('div[id^="detailView"]').block({ 
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
                                    $('div[id^="detailView"]').unblock();
                                },
                                success: function(response) {
                                    if (response.success) {
                                        $.toast({
                                            heading: 'Berhasil',
                                            text: response.success,
                                            showHideTransition: 'slide',
                                            icon: 'success',
                                            position: 'top-right',
                                            hideAfter: 3000
                                        });
                                        setTimeout(function() {
                                            location.reload(true);
                                        }, 1500);
                                    } else {
                                        $.toast({
                                            heading: 'Gagal',
                                            text: response.error,
                                            showHideTransition: 'fade',
                                            icon: 'error',
                                            position: 'top-right',
                                            hideAfter: 3000
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.error) {
                                        errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                    }
                                    // Handle specific errors if needed
                                    $.toast({  
                                        heading: 'Gagal',
                                        text: errorMessage,
                                        showHideTransition: 'fade',
                                        icon: 'error',
                                        position: 'top-right',
                                        hideAfter: 3000
                                    });
                                }
                            });
                        }
                    }
                });

                
            });

        });
    </script>
@endsection

@section('css')
    <style>
        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .custom-margin {
            margin-bottom: 5px;
        }

        .order-container {
            display: flex;
            align-items: stretch;
        }

        .order-image-link {
            display: block;
            height: 150px;
            width: 150px;
            border: 1px solid transparent;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .order-image {
            width: 100%;
            height: 100%;
            border-radius: 10px;
            object-fit: contain;
        }

        .order-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 100%;
        }

        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-name, .product-status, .product-service {
            margin: 0;
            font-size: 16px;
            text-align: left;
            flex: 1;
        }

        .product-price {
            margin: 0;
            font-size: 16px;
            text-align: right;
            white-space: nowrap;
        }

        .product-links {
            display: flex;
            margin-top: 10px;
            gap: 7px;
            align-items: flex-end;
        }

        .view-product, .view-similar, .view-track {
            cursor: pointer;
            border-color: #777;
            font-size: 14px;
            margin: 0;
            text-decoration: none;
        }

        /* For screens 768px and below (tablets) */
        /* Responsive layout for smaller screens (max-width: 768px) */
        @media (max-width: 1000px) and (min-width: 769px) {
            .order-details {
                max-width: 1000px; /* Limit the max-width to 1000px */
            }
        }

        @media (max-width: 900px) and (min-width: 769px) {
            .order-details {
                max-width: 900px; /* Limit the max-width to 800px */
            }
        }

        @media (max-width: 768px) {
            .product-info {
                flex-direction: column; /* Stack elements vertically */
                align-items: flex-start; /* Align items to the left */
            }

            .product-name, .product-status, .product-service {
                font-size: 14px; /* Adjust font size for smaller screens */
                width: 100%; /* Take full width */
                margin-bottom: 8px; /* Add space between elements */
                text-align: left; /* Align text to the left */
            }

            .product-price {
                font-size: 14px; /* Reduce font size for smaller screens */
                width: 100%; /* Ensure the price takes full width */
                text-align: left; /* Align the price to the left */
                margin-bottom: 8px; /* Add space below */
            }

            .product-links {
                flex-direction: column; /* Stack links vertically */
                gap: 5px; /* Adjust the gap between the links */
                align-items: flex-start; /* Align links to the left */
            }

            .view-product, .view-similar, .view-track {
                font-size: 12px; /* Reduce button font size */
                padding: 8px 12px; /* Adjust padding for touch-friendly size */
            }
        }

        /* Responsive layout for mobile screens (max-width: 480px) */
        @media (max-width: 480px) {
            .product-name, .product-status, .product-service, .product-price {
                font-size: 12px; /* Further reduce font size */
            }

            .view-product, .view-similar, .view-track {
                font-size: 12px; /* Reduce font size further for small screens */
                padding: 6px 10px; /* Smaller padding for buttons */
            }
        }

        .product-detail-info {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
            margin: 0 auto;
            display: none;
        }

        .product-detail-info .info-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .product-detail-info .info-text {
            flex-grow: 1;
        }

        .product-detail-info .info-table {
            /* width: 100%; */
            border-collapse: collapse;
        }

        .product-detail-info .label-column {
            font-weight: bold;
            color: #555;
            padding-right: 10px;
            white-space: nowrap;
        }

        .product-detail-info .value-column {
            padding-left: 10px;
        }

        .product-detail-info .info-image {
            max-width: 100px; 
            margin-left: 20px; 
        }

        .product-detail-info .payment-proof {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: transform 0.2s ease;
        }

        .product-detail-info .payment-proof:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            list-style-type: none;
            padding: 0;
            margin: 0;
            position: relative;
            margin-bottom: 20px;
        }

        .progress-container .step {
            text-align: center;
            flex: 1;
            position: relative;
        }

        .progress-container .step .icon {
            font-size: 30px;
            color: #ddd;
        }

        .progress-container .step.active .icon {
            color: #28a745;
        }

        .progress-container .step .line {
            position: relative;
            height: 4px;
            background: #ddd;
            border-radius: 0;
            margin-top: 25px;
            flex-grow: 1;
        }

        .progress-container .step.active .line {
            background: #28a745;
        }

        .progress-container .step .circle {
            width: 20px;
            height: 20px;
            background: #ddd;
            border-radius: 50%;
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: white;
        }

        .progress-container .step.active .circle {
            background: #28a745;
        }

        .progress-container .step .circle i {
            display: none;
        }

        .progress-container .step.active .circle i {
            display: inline;
        }

        .progress-container .step:first-child .line {
            border-radius: 5px 0 0 5px;
            margin-left: auto; /* Adjust as needed */
        }

        .progress-container .step:last-child .line {
            border-radius: 0 5px 5px 0;
            margin-right: auto; /* Adjust as needed */
        }

        .progress-container .step:not(:first-child):not(:last-child) .line {
            border-radius: 0; /* Remove border-radius for middle lines */
        }

        .detail-pesanan {
            /* display: block; */
            margin-top: 20px;
        }

        .timeline {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .timeline-item {
            position: relative;
            padding: 10px 0;
            /* display: flex; */
            align-items: flex-start; /* Align items to the start to align text with circles */
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ddd;
        }

        .timeline-item.active::after {
            background: #28a745;
        }

        .timeline-item:last-child::after {
            display: none;
        }

        .timeline-item.active::before {
            background: #28a745;
        }

        .timeline-item::after {
            content: "";
            position: absolute;
            top: 10px;
            left: 4px;
            width: 2px;
            height: 100%;
            background: #ddd;
        }

        .timeline-item:last-child::after {
            display: none;
        }

        .timeline-content {
            margin-top: -2%;
            margin-left: 30px;
            color:#777777; /* Adjust based on the size of the circle and spacing required */
        }

        .status-label {
            color: #28a745;
            font-weight: bold;
        }

        .rating {
            direction: rtl;
            unicode-bidi: bidi-override;
        }

        .rating > input {
            display: none;
        }

        .rating > label {
            color: #ddd;
            font-size: 30px;
            padding: 0;
            cursor: pointer;
        }

        .rating > input:checked ~ label,
        .rating > input:checked ~ label ~ label {
            color: #f5b301;
        }

        .rating > label:hover,
        .rating > label:hover ~ label {
            color: #f5b301;
        }

    </style>
@endsection