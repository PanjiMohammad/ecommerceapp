@extends('layouts.ecommerce')

@section('title')
    <title>Konfirmasi Pembayaran - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
    <section class="banner_area">
        <div class="banner_inner d-flex align-items-center">
            <div class="container">
                <div class="banner_content text-center">
                    <h2>Konfirmasi Pembayaran</h2>
                    <div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <a href="{{ route('customer.orders') }}">Konfirmasi Pembayaran</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Home Banner Area =================-->

    <!--================Login Box Area =================-->
    <section class="login_box_area p_100">
        <div class="container">
            @if (session('success'))
                <input type="hidden" id="success-message" value="{{ session('success') }}">
            @endif

            @if (session('error'))
                <input type="hidden" id="error-message" value="{{ session('error') }}">
            @endif

            <p>Total Tagihan : {{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</p>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mt-3">Info Produk</h3>
                        </div>
                        <div class="card-body">
                            @foreach($order->details as $detail)
                                <input type="hidden" name="invoice" value="{{ $order->invoice }}">
                                <input type="hidden" id="product_id" name="product_id" value="{{ $detail->product_id }}">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fa fa-user mr-1" style="font-size: 12px"></i>
                                    <span class="ml-1">{{ 'Penjual : ' . $detail->product->seller_name }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div style="height: 110px; width: 110px; display: block; border: 1px solid transparent;">
                                        <img class="image-product-detail" src="{{ asset('/products/' . $detail->product->image) }}" alt="{{ $detail->product->name }}">
                                    </div>
                                    <div class="d-flex flex-column ml-3">
                                        <span class="font-weight-bold mb-1">{{ $detail->product->name }}</span>
                                                <span class="mb-1">{{ $detail->qty . ' item x Rp ' . number_format($detail->price, 0, ',', '.') }}</span>
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
                                        <span class="mb-1">{{ $weightDisplay }}</span>
                                        <span class="mb-1">{{ 'Kurir : ' . $detail->shipping_service }}</span>
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            @if($snapToken)
                                <button id="pay-button" class="btn btn-primary btn-sm float-right">Bayar Sekarang</button>
                            @else
                                <span class="text-center text-danger">* Pembayaran tidak bisa diproses untuk sementara waktu, silahkan buat pesanan baru atau silakan menghubungi dukungan pelanggan.</span>
                            @endif
                        </div>
                    </div>
                    {{-- <div class="info-product-detail">
                        <h3>Info Produk</h3>
                        <hr>
                        @foreach($order->details as $detail)
                            <input type="hidden" name="invoice" value="{{ $order->invoice }}">
                            <input type="hidden" id="product_id" name="product_id" value="{{ $detail->product_id }}">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-user mr-1" style="font-size: 12px"></i>
                                <span class="ml-1">{{ 'Penjual : ' . $detail->product->seller_name }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div style="height: 125px; width: 125px; display: block; border: 1px solid transparent;">
                                    <img class="image-product-detail" src="{{ asset('/products/' . $detail->product->image) }}" alt="{{ $detail->product->name }}">
                                </div>
                                <div class="d-flex flex-column ml-3">
                                    <span class="font-weight-bold mb-1">{{ $detail->product->name }}</span>
                                            <span class="mb-1">{{ $detail->qty . ' item x Rp ' . number_format($detail->price, 0, ',', '.') }}</span>
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
                                    <span class="mb-1">{{ $weightDisplay }}</span>
                                    <span class="mb-1">{{ 'Kurir : ' . $detail->shipping_service }}</span>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                        <div class="row">
                            <div class="col-md-12">
                                
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="col-md-4">
                    @php 
                    
                        $serviceCost = 1000;
                        $packagingCost = 1000;
                        $subtotal = $order->subtotal;
                        $cost = $order->cost;
                        $grandTotal = $cost + $subtotal + $serviceCost + $packagingCost;
                    
                    @endphp
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa-regular fa-credit-card" style="font-size: 25px;"></i>
                                <span class="ml-2 font-weight-bold" style="font-size: 18px;">Rincian Pembayaran</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="mb-2">Subtotal {{ '(' . $order->details->count() . ' item)' }}</span>
                                <span class="mb-2">{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="mb-2">Ongkos Kirim</span>
                                <span class="mb-2">{{ 'Rp ' . number_format($order->cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="mb-2">Biaya Layanan</span>
                                <span class="mb-2">{{ 'Rp ' . number_format($order->service_cost, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="mb-2">Biaya Kemasan {{ '(' . $order->details->count('seller_id') . ' penjual)' }}</span>
                                <span class="mb-2">{{ 'Rp ' . number_format($order->packaging_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="mb-2 font-weight-bold">Total Tagihan</span>
                                <span class="mb-2 font-weight-bold">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="info-payment-detail">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fa-regular fa-credit-card" style="font-size: 25px;"></i>
                            <span class="ml-2 font-weight-bold" style="font-size: 18px;">Rincian Pembayaran</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="mb-2">Subtotal {{ '(' . $order->details->count() . ' item)' }}</span>
                            <span class="mb-2">{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="mb-2">Ongkos Kirim</span>
                            <span class="mb-2">{{ 'Rp ' . number_format($order->cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="mb-2">Biaya Layanan</span>
                            <span class="mb-2">{{ 'Rp ' . number_format($order->service_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="mb-2">Biaya Kemasan {{ '(' . $order->details->count('seller_id') . ' penjual)' }}</span>
                            <span class="mb-2">{{ 'Rp ' . number_format($order->packaging_cost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="info-payment-detail-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="mb-2 font-weight-bold">Total Tagihan</span>
                            <span class="mb-2 font-weight-bold">{{ 'Rp ' . number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
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

            // bayar
            var payButton = document.getElementById('pay-button');
            payButton.addEventListener('click', function () {

                var snapToken = "{{ $snapToken }}";

                if (!snapToken) {
                    alert("Snap token is missing. Please try again.");
                    return; // Stop further execution if snapToken is missing
                }
                // If the payment is pending, reopen the Snap popup automatically
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log(result);
                        $.toast({
                            heading: 'Berhasil',
                            text: 'Pembayaran Berhasil',
                            showHideTransition: 'fade',
                            icon: 'success',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                        setTimeout(function() {
                            window.location.href = "{{ route('customer.view_order', ['invoice' => $order->invoice]) }}";
                        }, 1000);
                    },
                    onPending: function(result) {
                        console.log(result);    
                        $.toast({
                            heading: 'Menunggu',
                            text: 'Pembayaran Belum Selesai, harap selesaikan terlebih dahulu.',
                            showHideTransition: 'fade',
                            icon: 'info',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                    },
                    onError: function(result) {
                        console.log(result);
                        $.toast({
                            heading: 'Gagal',
                            text: 'Pembayaran Gagal',
                            showHideTransition: 'fade',
                            icon: 'error',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                    },
                    onClose: function() {
                        $.toast({
                            heading: 'Pemberitahuan',
                            text: 'Anda menutup popup tanpa menyelesaikan pembayaran',
                            showHideTransition: 'fade',
                            icon: 'warning',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                    }
                });
            });

        });
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
    <style>
        .info-product-detail {
            border: 1px solid #ededed;
            padding: 20px;
            border-radius: 4px;
        }
        .image-product-detail {
            height: 100%;
            width: 100%;
            object-fit: contain;
            border-radius: 5px;
        }
        .info-payment-detail {
            padding: 15px 20px 10px;
            background-color: #ededed;
            border: 1px solid #ededed;
        }
        .info-payment-detail-2 {
            padding: 15px 20px 10px;
            background-color: white;
            border: 1px solid #ededed;
        }
    </style>    
@endsection