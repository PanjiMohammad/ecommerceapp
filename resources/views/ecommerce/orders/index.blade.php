@extends('layouts.ecommerce')

@section('title')
    <title>Daftar Pesanan - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Daftar Pesanan</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <a href="{{ route('customer.orders') }}">Daftar Pesanan</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
	<section class="login_box_area p_120">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    <div class="row">
						<div class="col-md-12">
							<div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mt-3">Daftar Pesanan</h4>
                                </div>
								<div class="card-body">
                                    @if (session('success'))
                                        <input type="hidden" id="success-message" value="{{ session('success') }}">
                                    @endif

                                    @if (session('error'))
                                        <input type="hidden" id="error-message" value="{{ session('error') }}">
                                    @endif
									
                                    @if($orders->count() > 0)
                                        @php $i = 1; @endphp
                                        @foreach($orders as $key => $order)
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
                                                            <a class="btn btn-outline-info btn-sm lihat-test mr-1" data-invoice="{{ $order->invoice }}" data-target="#invoiceModal-{{ $order->invoice }}">Lihat Produk</a>
                                                            {{-- <a class="btn btn-outline-info btn-sm lihat-produk mr-1" data-toggle="modal" data-target="#productModal" data-order="{{ $key }}">Lihat Produk</a> --}}
                                                            <a href="{{ route('customer.view_order', $order->invoice) }}" class="btn btn-outline-primary btn-sm">Lihat Pesanan</a>
                                                            {{-- <a href="{{ route('customer.view_order', $order->invoice) }}" class="btn btn-outline-info btn-sm">Lihat Pesanan</a> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Lihat Invoice Modal -->
                                            <div class="modal fade" id="invoiceModal-{{ $order->invoice }}" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" style="max-width: 95%;" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Pesanan : <span id="invoiceModalLabel-{{ $order->invoice }}"></span></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Produk</th>
                                                                            <th>Harga</th>
                                                                            <th>Kuantiti</th>
                                                                            <th>Berat</th>
                                                                            <th>Subtotal</th>
                                                                            <th>Nomor Resi</th>
                                                                            <th>Kurir</th>
                                                                            <th>Ongkos Kirim</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($order->details as $key => $detail)
                                                                        @php

                                                                            // product-return
                                                                            $productReturn = $order->return->first(function ($return) use ($detail) {
                                                                                return $return->order_id === $detail->order_id && $return->product_id === $detail->product_id;
                                                                            });
                                                                            
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
                                                                        <tr>
                                                                            <td>
                                                                                <div class="d-flex align-items-center">
                                                                                    <div style="width: 80px; height: 80px; display: block; border: 1px solid transparent;">
                                                                                        <img class="d-block rounded" src="{{ asset('/products/' . $detail->product->image) }}" alt="{{ $detail->product->name }}" style="height: 100%; width: 100%; object-fit: contain; border-radius: 4px;">
                                                                                    </div>    
                                                                                    <div class="d-flex flex-column ml-3">
                                                                                        <span class="font-weight-bold">{{ $detail->product->name }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="font-weight-bold align-middle">{{ 'Rp ' . number_format($detail->price, 0, ',', '.') }}</td>
                                                                            <td class="font-weight-bold align-middle">{{ $detail->qty . ' item' }}</td>
                                                                            <td class="font-weight-bold align-middle">{{ $weightDisplay }}</td>
                                                                            <td class="font-weight-bold align-middle">{{ 'Rp ' . number_format($detail->price * $detail->qty, 0, ',', '.') }}</td>
                                                                            <td class="font-weight-bold align-middle">
                                                                                @if($detail->tracking_number != null)
                                                                                    {{ '#' . $detail->tracking_number }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td class="font-weight-bold align-middle">{{ $detail->shipping_service }}</td>
                                                                            <td class="font-weight-bold align-middle">{{ 'Rp ' . number_format($detail->shipping_cost, 0, ',', '.') }}</td>
                                                                            <td class="align-middle">
                                                                                @if($productReturn)
                                                                                    {!! $productReturn->status_label !!}
                                                                                @else
                                                                                    {!! $detail->status_label !!}
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @php $i++; @endphp
                                        @endforeach
                                    @else
                                        <h4 class="text-center">Tidak ada pesanan</h4>
                                    @endif

                                    <div class="float-right mt-3">
                                        {!! $orders->links() !!}
                                    </div>
                                </div>
                                
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

    <!-- Lihat Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 90%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pesanan : <span id="productModalLabel"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Kuantiti</th>
                                    <th>Berat</th>
                                    <th>Subtotal</th>
                                    <th>Nomor Resi</th>
                                    <th>Kurir</th>
                                    <th>Ongkos Kirim</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="productDetails">
                                <!-- Details will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
            
            // new
            $('.lihat-test').on('click', function() {
                console.log('test');
                var invoice = $(this).data('invoice');
                $('#invoiceModal-' + invoice).modal('show');
                $('#invoiceModalLabel-' + invoice).text(invoice);
            });

            $('.lihat-produk').on('click', function() {
                var orderId = $(this).data('order');
                var orders = @json($orders->getCollection());

                var getInvoice = orders[orderId];
                console.log(getInvoice.invoice);
                $('#productModalLabel').text(getInvoice.invoice)

                var orderDetails = @json($orders->getCollection()->map->details)

                var details = orderDetails[orderId];
                var tableBody = $('#productDetails');
                tableBody.empty();

                details.forEach(function(detail) {
                    var product = detail.product ? detail.product.name : 'Produk tidak tersedia';
                    var image = detail.product ? detail.product.image : '/proof/payment/no-payment.jpeg';
                    var price = detail.price ?? 0;
                    var qty = detail.qty ?? 0;
                    var weight = detail.weight ?? 0;
                    var shippingCost = detail.shipping_cost ?? 0;
                    var service = detail.shipping_service ?? 0;
                    var total = (price * qty);
                    var trackingNumber = detail.tracking_number ? '#' + detail.tracking_number : '-';
                    var statusLabel = detail.status_label;

                    // Ensure weight is treated as a string
                    weight = weight.toString();

                    // Handle weight range or single value
                    var weightDisplay = '';
                    if (weight.indexOf('-') !== -1) {
                        var weights = weight.split('-').map(w => parseFloat(w.trim()));
                        var minWeightDisplay = weights[0] >= 1000 ? (weights[0] / 1000) : weights[0];
                        var maxWeightDisplay = weights[1] >= 1000 ? (weights[1] / 1000) + ' Kg' : weights[1] + ' gram / pack';
                        weightDisplay = minWeightDisplay + ' - ' + maxWeightDisplay;
                    } else {
                        weightDisplay = parseFloat(weight) >= 1000 ? (parseFloat(weight) / 1000) + ' Kg' : weight + ' gram / pack';
                    }

                    tableBody.append(`
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div style="width: 80px; height: 80px; display: block; border: 1px solid transparent;">
                                        <img class="d-block rounded" src="{{ asset('/products/') }}/${image}" alt="${product}" style="height: 100%; width: 100%; object-fit: contain; border-radius: 4px;">
                                    </div>    
                                    <div class="d-flex flex-column ml-3">
                                        <span class="font-weight-bold">${product}</span>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align: middle;" class="font-weight-bold">Rp ${price.toLocaleString('id')}</td>
                            <td style="vertical-align: middle;" class="font-weight-bold">${qty} item</td>
                            <td style="vertical-align: middle;" class="font-weight-bold">${weightDisplay} item</td>
                            <td style="vertical-align: middle;" class="font-weight-bold">Rp ${total.toLocaleString('id')}</td>
                            <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">${trackingNumber}</td>
                            <td style="vertical-align: middle;" class="font-weight-bold">${service}</td>
                            <td style="vertical-align: middle;" class="font-weight-bold">Rp ${shippingCost.toLocaleString('id')}</td>
                            <td style="vertical-align: middle;">${statusLabel}</td>
                        </tr>
                    `);
                });
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
            object-fit: cover;
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
    </style>
@endsection