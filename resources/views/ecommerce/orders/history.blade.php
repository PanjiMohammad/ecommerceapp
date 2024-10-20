@extends('layouts.ecommerce')

@section('title')
    <title>Riwayat Pesanan - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Riwayat Pesanan</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Beranda</a>
                        <a href="{{ route('customer.history') }}">Riwayat Pesanan</a>
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
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mt-3">Riwayat Pesanan</h4>
                        </div>
                        <div class="card-body">
                            @if($orders->count() > 0)
                                @php $i = 1; @endphp
                                @foreach($orders as $order)
                                    @php
                                        $detailsCount = $order->details->count();
                                    @endphp
                                    <div class="order_history">
                                        <div class="order_card mb-3">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-bag-shopping" style="font-size: 20px;"></i>
                                                    <div class="ml-2 d-flex flex-column">
                                                        <span class="d-block">Belanja</span>
                                                        <span class="d-block" style="margin-top: -5px;">{{ \Carbon\Carbon::parse($order->created_at)->locale('id')->translatedFormat('l, d F Y') }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="text-uppercase font-weight-bold">{{ $order->invoice  }}</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="d-flex align-items-center">
                                                <div class="whatsapp-frame">
                                                    <!-- Apply grid only if there are more than 1 image -->
                                                    @if($detailsCount > 1)
                                                        <div class="image-grid">
                                                            @foreach($order->details->take(3) as $index => $detail) <!-- Show first 3 images -->
                                                                <div class="image-item">
                                                                    <img src="{{ asset('/products/' . $detail->product->image) }}" alt="{{ $detail->product->name }}">
                                                                </div>
                                                            @endforeach
                
                                                            <!-- If more than 4 images, show +X in the fourth slot -->
                                                            @if($detailsCount > 4)
                                                                <div class="image-item more-images">
                                                                    <span class="more-count">+{{ $detailsCount - 4 }}</span>
                                                                </div>
                                                            @elseif($detailsCount == 4)
                                                                <div class="image-item">
                                                                    <img src="{{ asset('/products/' . $order->details[3]->product->image) }}" alt="{{ $order->details[3]->product->name }}">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <!-- Show a single image without the grid -->
                                                        @foreach($order->details->take(1) as $detail) <!-- Show only the first image -->
                                                            <div class="single-image">
                                                                <img src="{{ asset('/products/' . $detail->product->image) }}" alt="{{ $detail->product->name }}">
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="ml-3">
                                                    <span class="font-weight-bold">{{ 'Total ' . $order->details->count() . ' Produk :'}}</span>
                                                    @php
                                                        
                                                        $productName = [];
                                                        foreach($order->details as $detail){
                                                            $productName[] = $detail->product->name;
                                                        }

                                                        // Get all elements except the last one
                                                        $allButLast = array_slice($productName, 0, -1);
                                                        $last = end($productName);

                                                        $result = implode(', ', $allButLast) . (count($allButLast) > 0 ? ' & ' : '') . $last;
                                                        
                                                    @endphp
                                                    <span>{{ $result . '.' }}</span>
                                                </div>
                                            </div>
                                            <hr class="mt-3 mb-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                @php

                                                    $countItems = $order->details->count();
                                                    $countSellers = $order->details->groupBy('seller_id')->count();
                                                    $packagingCost = $order->details->groupBy('seller_id')->count() * 1000;
                                                    $serviceCost = 1000;
                                                    $subtotal = 0;
                                                    $shippingCost = $order->details->unique('seller_id')->sum('shipping_cost');
                                                    $grandTotal = 0;

                                                    foreach($order->details as $detail){
                                                        $items = $detail->price * $detail->qty;
                                                        $subtotal += $items;
                                                    }

                                                    $grandTotal += $subtotal + $shippingCost + $packagingCost + $serviceCost;

                                                @endphp
                                                <div class="order-total-price">
                                                    <span class="font-weight-bold">{{ 'Total Belanja : Rp ' . number_format($grandTotal, 0, ',', '.') }}</span>
                                                </div>
                                                <div>
                                                    <a href="#" class="btn btn-outline-primary btn-sm" title="Detail Pesanan {{ $order->invoice }}" data-toggle="modal" data-target="#invoiceModal{{ $order->invoice }}">Lihat Pesanan</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Invoice -->
                                    <div class="modal fade" id="invoiceModal{{ $order->invoice }}" tabindex="-1" role="dialog" aria-labelledby="invoiceModal{{ $order->invoice }}" aria-hidden="true">
                                        <div class="modal-dialog" style="max-width: 80%;" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" style="margin-left: 15px;" id="invoiceModalLabel{{ $order->invoice }}">Detail Pesanan</h5>
                                                    <button type="button" class="close" style="margin-right: 5px;" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div style="padding: 0 15px;">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <p style="margin-bottom: 0;">Invoice : <span class="text-uppercase font-weight-bold">{{ $order->invoice }}</span></p>
                                                            <a href="{{ route('customer.history_pdf', $order->invoice) }}" class="text-primary viewInvoice" data-index="{{ $i }}" data-invoice="{{ $order->invoice }}">Lihat Invoice</a>
                                                        </div>
                                                        <div class="show-preview-invoice" id="invoicePreview{{ $i }}">
                                                            <!-- The iframe will be injected here -->
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <p style="margin-bottom: 0;">Tanggal Pembelian</p>
                                                            <p style="margin-bottom: 0;">{{ \Carbon\Carbon::parse($order->created_at)->locale('id')->translatedFormat('l, d F Y H:i') . ' WIB' }}</p>
                                                        </div>
                                                        <hr>
                                                        <div class="product-info mb-4">
                                                            <h5>Detail Produk</h5>
                                                            @foreach($order->details as $index => $detail)
                                                                <div style="border: 1px solid #ddd; padding: 15px; border-radius: 4px; background-color: #fff; margin-bottom: 15px; margin-top: 10px;">
                                                                    <div class="d-flex align-item-center justify-content-between">
                                                                        <p style="margin-bottom: 0;" class="font-weight-bold">{{ 'Nomor Resi : #' . $detail->tracking_number }}</p>
                                                                        @php
                                                                            // Find the return specific to this product and order (if exists)
                                                                            $productReturn = $order->return->first(function ($return) use ($detail) {
                                                                                return $return->order_id === $detail->order_id && $return->product_id === $detail->product_id;
                                                                            });
                                                                        @endphp

                                                                        @if($productReturn)
                                                                            <p style="margin-bottom: 0;"><span class="font-weight-bold">Return :</span> {!! $productReturn->status_label !!}</p>
                                                                        @else
                                                                            <p style="margin-bottom: 0;">{!! $detail->status_label !!}</p>
                                                                        @endif
                                                                    </div>
                                                                    <hr>
                                                                    <div class="d-flex align-item-center">
                                                                        <div style="height: 100px; widows: 100px; border: 1px solid transparent; display: block;">
                                                                            <img style="height: 100%; widows: 100%; object-fit: contain; border-radius: 4px;" src="{{ asset('/products/' . $detail->product->image) }}" alt="{{ $detail->product->name }}">
                                                                        </div>
                                                                        <div style="margin-top: 15px; margin-left: 15px; vertical-align: middle; align-items: center;">
                                                                            <p class="mb-1"><strong>{{ $detail->product->name }}</strong></p>
                                                                            <p class="mb-1">{{ $detail->qty . ' item x Rp ' . number_format($detail->price, 0, ',', '.') }}</p>
                                                                            @php
                                                                                $weight = $detail->weight;
            
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
                                                                            <p class="mb-0">{{ 'Berat : ' . $weightDisplay }}</p>
                                                                        </div>
                                                                    </div>
                                                                    <hr class="mt-3">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div>
                                                                            <span class="font-weight-bold">{{ 'Total Harga : Rp ' . number_format($detail->qty * $detail->price, 0, ',', '.') }}</span>
                                                                        </div>
                                                                        <div>
                                                                            <a href="#" class="toggle-details btn btn-outline-info btn-sm mr-1" data-index="{{ $index }}">Lacak Produk</a>
                                                                            <a href="{{ url('/product/' . $detail->product->slug) }}" class="btn btn-sm btn-outline-primary">Beli Lagi</a>
                                                                        </div>
                                                                    </div>

                                                                    <div class="detail-pesanan-{{ $index }} mb-4 mt-3" style="display: none; padding: 30px 30px 0 30px; border: 1px solid #ededed; border-radius: 5px; background-color: #fff;">
                                                                        <ul class="progress-container">
                                                                            <li class="step active">
                                                                                <div class="icon"><i class="fas fa-box"></i></div>
                                                                                <div class="line">
                                                                                    <div class="circle active"><i class="fas fa-check"></i></div>
                                                                                </div>
                                                                            </li>
                                                                            <li class="step active">
                                                                                <div class="icon"><i class="fas fa-truck"></i></div>
                                                                                <div class="line">
                                                                                    <div class="circle active"><i class="fas fa-check"></i></div>
                                                                                </div>
                                                                            </li>
                                                                            <li class="step active">
                                                                                <div class="icon"><i class="fas fa-handshake"></i></div>
                                                                                <div class="line">
                                                                                    <div class="circle active"><i class="fas fa-check"></i></div>
                                                                                </div>
                                                                            </li>
                                                                            <li class="step active">
                                                                                <div class="icon"><i class="fas fa-archive"></i></div>
                                                                                <div class="line">
                                                                                    <div class="circle active"><i class="fas fa-check"></i></div>
                                                                                </div>
                                                                            </li>
                                                                        </ul>
                                                                        <div class="detail-pesanan-info mt-5 mb-5">
                                                                            {{-- <p style="font-size: 20px; color: green;" class="text-center">Pesanan Selesai</p> --}}
                                                                            @if($detail->status >= 6)
                                                                                @if($productReturn)
                                                                                    <p style="font-size: 20px; color: green;" class="text-center">Pesanan Selesai & Return Disetujui Oleh Penjual</p>
                                                                                @else
                                                                                    <p style="font-size: 20px; color: green;" class="text-center">Pesanan Selesai</p>
                                                                                @endif
                                                                            @elseif($detail->status >= 5)
                                                                                @if($productReturn)
                                                                                    <p style="font-size: 20px; color: green;" class="text-center">Menunggu Konfirmasi Return Dari Penjual</p>
                                                                                @else
                                                                                    <p style="font-size: 20px; color: green;" class="text-center">Pesanan Sampai</p>
                                                                                @endif    
                                                                            @elseif ($detail->status == 4)
                                                                                <p style="font-size: 20px; color: green;" class="text-center">Pesanan Sedang Dikirim</p>
                                                                            @elseif ($detail->status == 3 || $detail->status == 2)
                                                                                <p style="font-size: 20px; color: color: green;" class="text-center">Pembayaran Sudah Dikonfirmasi & Pesanan dalam proses pengemasan</p>
                                                                            @elseif ($detail->status == 1)
                                                                                <p style="font-size: 20px; color: green;" class="text-center">Menunggu Konfirmasi dari Penjual</p>
                                                                            @else
                                                                                <p style="font-size: 20px; color: #777;" class="text-center">Pesan Anda Belum Diproses</p>
                                                                            @endif
                                                                        </div>
                                                                        <div class="summary-order-product">
                                                                            <h4>Status Pemesanan</h4>
                                                                            <hr>
                                                                            <ul class="timeline">
                                                                                {{-- @if($detail->status_return)
                                                                                    @if($detail->status >= 6 && $detail->status_return == 1)
                                                                                        <li class="timeline-item {{ $detail->status >= 6 ? 'active' : '' }}" data-step="6">
                                                                                            <div class="timeline-content">
                                                                                                <div class="d-flex justify-content-between">
                                                                                                    <p>System-Automatic - {{ \Carbon\Carbon::parse($detail->receive_date)->locale('id')->translatedFormat('l, d F Y') }}<br> Return disetujui & pesanan telah dikembalikan ke penjual <span class="text-capitalize">{{ $detail->product->seller->name }}</span></p>
                                                                                                    <p>{{ \Carbon\Carbon::parse($detail->receive_date)->locale('id')->translatedFormat('H:i') }} WIB</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </li>
                                                                                    @endif
                                                                                @else
                                                                                    @if($detail->status >= 6)
                                                                                        <li class="timeline-item {{ $detail->status >= 6 ? 'active' : '' }}" data-step="6">
                                                                                            <div class="timeline-content">
                                                                                                <div class="d-flex justify-content-between">
                                                                                                    <p>System-Automatic - {{ \Carbon\Carbon::parse($detail->receive_date)->locale('id')->translatedFormat('l, d F Y') }}<br> Pesanan selesai & telah diterima oleh <span class="text-capitalize">{{ $order->customer_name }}</span></p>
                                                                                                    <p>{{ \Carbon\Carbon::parse($detail->receive_date)->locale('id')->translatedFormat('H:i') }} WIB</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </li>
                                                                                    @endif
                                                                                @endif --}}
                                                                                
                                                                                @if($productReturn)
                                                                                    @if($detail->status >= 6 && $productReturn->status >= 1)
                                                                                        <li class="timeline-item {{ $detail->status >= 6 && $productReturn->status >= 1 ? 'active' : '' }}" data-step="6">
                                                                                            <div class="timeline-content">
                                                                                                <div class="d-flex justify-content-between">
                                                                                                    <p>System-Automatic - {{ \Carbon\Carbon::parse($detail->receive_date)->locale('id')->translatedFormat('l, d F Y') }}<br> Return disetujui & pesanan telah dikembalikan ke penjual <span class="text-capitalize">{{ $detail->product->seller->name }}</span></p>
                                                                                                    <p>{{ \Carbon\Carbon::parse($productReturn->updated_at)->locale('id')->translatedFormat('H:i') }} WIB</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </li>
                                                                                    @endif
                                                                                @else
                                                                                    @if($detail->status >= 6)
                                                                                        <li class="timeline-item {{ $detail->status >= 6 ? 'active' : '' }}" data-step="6">
                                                                                            <div class="timeline-content">
                                                                                                <div class="d-flex justify-content-between">
                                                                                                    <p>System-Automatic - {{ \Carbon\Carbon::parse($detail->receive_date)->locale('id')->translatedFormat('l, d F Y') }}<br> Pesanan selesai & telah diterima oleh <span class="text-capitalize">{{ $order->customer_name }}</span></p>
                                                                                                    <p>{{ \Carbon\Carbon::parse($detail->receive_date)->locale('id')->translatedFormat('H:i') }} WIB</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </li>
                                                                                    @endif
                                                                                @endif

                                                                                {{-- @if($detail->status >= 5 && $detail->return)
                                                                                <li class="timeline-item {{ $detail->status >= 5 && $detail->status_return == 0 ? 'active' : '' }}" data-step="5">
                                                                                    <div class="timeline-content">
                                                                                        <div class="d-flex justify-content-between">
                                                                                            <p style="color: #777777">User - {{ $detail->formatted_arrived_date_day }} <br> Sedang mengajukan return pada pesanan ini.</p>
                                                                                            <p>{{ $detail->formatted_arrived_date }} WIB</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                                @endif --}}
                                                                                @if($detail->status >= 5)
                                                                                    @if($productReturn)
                                                                                        <li class="timeline-item" data-step="5">
                                                                                            <div class="timeline-content">
                                                                                                <div class="d-flex justify-content-between">
                                                                                                    <p style="color: #777777">System-Automatic - {{ \Carbon\Carbon::parse($detail->arrive_date)->locale('id')->translatedFormat('l, d F Y') }} <br> <span class="text-danger">Sedang mengajukan return pada pesanan ini.</span></p>
                                                                                                    <p>{{ \Carbon\Carbon::parse($productReturn->created_at)->locale('id')->translatedFormat('H:i') }} WIB</p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </li>
                                                                                    @endif
                                                                                    <li class="timeline-item" data-step="5">
                                                                                        <div class="timeline-content">
                                                                                            <div class="d-flex justify-content-between">
                                                                                                <p style="color: #777777">System-Automatic - {{ \Carbon\Carbon::parse($detail->arrive_date)->locale('id')->translatedFormat('l, d F Y') }} <br> Pesanan tiba.</p>
                                                                                                <p>{{ \Carbon\Carbon::parse($detail->arrive_date)->locale('id')->translatedFormat('H:i') }} WIB</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                @endif
                                                                                @if($detail->status >= 4)
                                                                                    <li class="timeline-item" data-step="4">
                                                                                        <div class="timeline-content">
                                                                                            <div class="d-flex justify-content-between">
                                                                                                <p>System-Automatic - {{ \Carbon\Carbon::parse($detail->shippin_date)->locale('id')->translatedFormat('l, d F Y') }} <br> Pesanan sedang dikirim.</p>
                                                                                                <p>{{ \Carbon\Carbon::parse($detail->shippin_date)->locale('id')->translatedFormat('H:i') }} WIB</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                @endif
                                                                                @if($detail->status >= 3)
                                                                                    <li class="timeline-item" data-step="3">
                                                                                        <div class="timeline-content">
                                                                                            <div class="d-flex justify-content-between">
                                                                                                <p class="text-justify">Seller - {{ \Carbon\Carbon::parse($detail->process_date)->locale('id')->translatedFormat('l, d F Y') }}<br>Pesanan sedang dalam proses pengemasan.</p>
                                                                                                <p>{{ \Carbon\Carbon::parse($detail->process_date)->locale('id')->translatedFormat('H:i') }} WIB</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                @endif
                                                                                @if($detail->status >= 2)
                                                                                    <li class="timeline-item" data-step="2">
                                                                                        <div class="timeline-content">
                                                                                            <div class="d-flex justify-content-between">
                                                                                                <p>Seller - {{ \Carbon\Carbon::parse($detail->formatted_confirm_payment_date)->locale('id')->translatedFormat('l, d F Y') }}<br>Pembayaran kamu sudah dikonfirmasi.</p>
                                                                                                <p>{{ \Carbon\Carbon::parse($detail->formatted_confirm_payment_date)->locale('id')->translatedFormat('H:s') }} WIB</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                @endif
                                                                                @if($detail->status >= 1)
                                                                                    <li class="timeline-item" data-step="1">
                                                                                        <div class="timeline-content">
                                                                                            <div class="d-flex justify-content-between">
                                                                                                <p class="text-justify">System-Automatic - {{ \Carbon\Carbon::parse($detail->shop_date)->locale('id')->translatedFormat('l, d F Y') }}<br>Menunggu Penjual Mengkonfirmasi Pembayaran Kamu.</p>
                                                                                                <p>{{ \Carbon\Carbon::parse($detail->shop_date)->locale('id')->translatedFormat('H:s') }} WIB</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                @endif
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <hr>
                                                        <div class="product-info-shipping">
                                                            <h5>Info Pengiriman</h5>
                                                            <div class="table-responsive">
                                                                @php
                                                                    $shippingService = $order->details->pluck('shipping_service')->unique()->values();
                                                                
                                                                    if ($shippingService->count() === 1) {
                                                                        $serviceName = $shippingService->first(); // All services are the same
                                                                    } else {
                                                                        $lastService = $shippingService->pop();
                                                                        $serviceName = $shippingService->implode(', ') . ' & ' . $lastService;
                                                                    }
                                                                @endphp
                                                                <table>
                                                                    <tr>
                                                                        <td style="width: 40%;">Kurir <i class="fa-solid fa-copy ml-1"></i></td>
                                                                        <td>{{ $serviceName }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="width: 40%;">Alamat <i class="fa-solid fa-copy ml-1"></i></td>
                                                                        <td><span class="text-capitalize font-weight-bold">{{ $order->customer_name }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="width: 40%;"></td>
                                                                        <td><span class="text-uppercase">{{ $order->customer_phone }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="width: 40%;"></td>
                                                                        <td>
                                                                            <span class="text-capitalize text-justify" style="text-align: justify">{{ $order->customer_address . ', Kecamatan ' . $order->customer->district->name . ', Kota ' . $order->customer->district->city->name . ', ' . $order->customer->district->city->province->name . ', Kode Pos ' . $order->customer->district->city->postal_code . ', Indonesia' }}</span>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <p style="margin-bottom: 0;">Metode Pembayaran</p>
                                                            <p style="margin-bottom: 0;" class="font-weight-bold">{!! $order->payment->payment_method_name . ' - ' . $order->payment->acquirer_name !!}</p>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <p style="margin-bottom: 0;">Tanggal Pembayaran</p>
                                                            <p style="margin-bottom: 0;" class="font-weight-bold">{{ \Carbon\Carbon::parse($order->payment->created_at)->locale('id')->translatedFormat('l, d F Y H:i') }}</p>
                                                        </div>
                                                        <hr>
                                                        <div class="shopping-total-summary">
                                                            @php

                                                                $countItems = $order->details->count();
                                                                $countSellers = $order->details->groupBy('seller_id')->count();
                                                                $packagingCost = $order->details->groupBy('seller_id')->count() * 1000;
                                                                $serviceCost = 1000;
                                                                $subtotal = 0;
                                                                $shippingCost = $order->details->unique('seller_id')->sum('shipping_cost');
                                                                $grandTotal = 0;

                                                                foreach($order->details as $index => $detail){
                                                                    $items = $detail->price * $detail->qty;
                                                                    $subtotal += $items;
                                                                }

                                                                $grandTotal = $subtotal + $shippingCost + $packagingCost + $serviceCost;

                                                            @endphp
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <p style="margin-bottom: 5px;">Subtotal {{ '(' . $countItems . ' item)' }}</p>
                                                                <p style="margin-bottom: 5px;">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</p>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <p style="margin-bottom: 5px;">Ongkos Kirim</p>
                                                                <p style="margin-bottom: 5px;">{{ 'Rp ' . number_format($shippingCost, 0, ',', '.') }}</p>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <p style="margin-bottom: 5px;">Biaya Layanan</p>
                                                                <p style="margin-bottom: 5px;">{{ 'Rp ' . number_format($order->service_cost, 0, ',', '.') }}</p>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <p style="margin-bottom: 0;">Biaya Kemasan {{ '(' . $countSellers . ' penjual)' }}</p>
                                                                <p style="margin-bottom: 0;">{{ 'Rp ' . number_format($packagingCost, 0, ',', '.') }}</p>
                                                            </div>
                                                            <hr>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <p style="margin-bottom: 0;" class="font-weight-bold">Total Belanja</p>
                                                                <p style="margin-bottom: 0;" class="font-weight-bold">{{ 'Rp ' . number_format($grandTotal, 0, ',', '.') }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div style="padding: 0 15px;">
                                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                                                        {{-- <a href="{{ url('/product/' . $detail->product->slug) }}" class="btn btn-primary">Lihat Produk</a> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end of Modal Invoice -->
                                    @php $i++; @endphp
                                @endforeach


                            @else
                                <h4>Tidak ada riwayat pesanan</h4>
                            @endif

                            <div class="float-right mt-3">
                                {!! $orders->links() !!}
                            </div>
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

            // handle session
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

            $(document).on('click', '.toggle-details', function(e) {
                e.preventDefault();
                var index = $(this).data('index');
                console.log('ini index : ' + index);
                $(this).closest('.d-flex').next('.detail-pesanan-' . index).toggle();
            });

            $('#show-payment-details').on('click', function(){
                $('.payment-detail-info').toggle(); // Toggle between show and hide
            });
            
            $(document).on('click', '.viewInvoice', function(e) {
                console.log('berhasil');
                e.preventDefault(); // Prevent the default link behavior
                var invoiceId = $(this).data('invoice'); // Get the invoice ID
                var index = $(this).data('index'); // Get the unique identifier
                var $invoicePreview = $('#invoicePreview' + index);
                var pdfUrl = $(this).attr('href'); // Get the URL of the PDF

                // Check if the iframe already exists, if it does, remove it
                if ($invoicePreview.find('iframe').length > 0) {
                    $invoicePreview.html(''); // Remove the iframe if it already exists
                } else {
                    // Fetch the PDF via AJAX
                    $.ajax({
                        url: pdfUrl,
                        type: 'GET',
                        beforeSend: function() {
                            $('#invoicePreview' + index).block({ 
                                message: '<i class="fa fa-spinner fa-spin"></i> Sedang memuat...',
                                overlayCSS: {
                                    backgroundColor: '#fff',
                                    opacity: 0.8,
                                    cursor: 'wait'
                                },
                                css: {
                                    border: 0,
                                    padding: 0,
                                    backgroundColor: 'none',
                                    '-webkit-border-radius': '10px', 
                                    '-moz-border-radius': '10px', 
                                }
                            });
                        },
                        complete: function() {
                            $('#invoicePreview' + index).unblock();
                        },
                        success: function(response) {
                            $.toast({
								heading: 'Berhasil',
								text: response.success,
								showHideTransition: 'fade',
								icon: 'success',
								position: 'top-right',
								hideAfter: 2000
							});
                            setTimeout(function() {
                                $invoicePreview.html('<iframe src="' + pdfUrl + '" width="100%" height="900px" class="mb-2"></iframe>');
                            }, 1500);
                        },
                        error: function(xhr, status, error) {
                            console.log('Error loading PDF: ', error);
                        }
                    });
                }
            });

        });
    </script>
@endsection

@section('css')
    <style>

        .order_card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #fff;
        }
        
        .whatsapp-frame {
            display: inline-block;
            padding: 5px;
            background-color: #fff;
            border-radius: 5px;
            border: 1px solid #ddd;
            max-width: 100%; /* Remove fixed width, let the content adjust */
        }

        .image-grid {
            display: grid;
            gap: 5px;
            grid-template-columns: repeat(2, 1fr);
            width: 100%;
            justify-content: center;
        }

        .image-item {
            position: relative;
            width: 70px;
            height: 70px;
            border: 1px solid #ededed;
            border-radius: 4px;
            overflow: hidden;
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 4px;
        }

        .more-images {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 4px;
        }

        .more-count {
            color: white;
            font-size: 18px;
            font-weight: bold;
        }

        .single-image {
            width: 70px;
            height: 70px;
            border: 1px solid #ededed;
            border-radius: 4px;
        }

        .single-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .order_card h4 {
            margin-bottom: 15px;
        }

        .order_card hr {
            margin-top: 0;
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
            top: -7px;
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

        .timeline-item.active::before {
            background: #28a745;
        }

        .timeline-item.active::after {
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
            margin-top: -1.7%;
            margin-left: 30px;
            color:#777777; /* Adjust based on the size of the circle and spacing required */
        }

        .status-label {
            color: #28a745;
            font-weight: bold;
        }

    </style>
@endsection
