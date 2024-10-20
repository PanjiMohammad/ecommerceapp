@extends('layouts.ecommerce')

@section('title')
    <title>Keranjang Belanja - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Keranjang Belanja</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <a href="{{ route('front.list_cart') }}">Keranjang</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Cart Area =================-->
	<section class="cart_area">
		<div class="container">
			<div class="row">
				<div class="col-lg-8">
					<div class="card">
						<div class="card-body">
							<div class="cart_inner">
								<form action="{{ route('front.update_cart') }}" method="post">
									@csrf  
									@if (session('success'))
										<input type="hidden" id="success-message" value="{{ session('success') }}">
									@endif
				
									@if (session('error'))
										<input type="hidden" id="error-message" value="{{ session('error') }}">
									@endif
									<div id="input-cost">
										<!-- // -->
									</div>          
									{{-- <div class="d-flex justify-content-between align-items-center mb-3" style="background-color: white; padding: 10px 10px 0 10px;">
										<div class="d-flex">
											<label class="custom-checkbox-container">
												<input type="checkbox" name="selectAll" id="selectAll">
												<span class="custom-checkbox"></span>
											</label>
											<span class="ml-2 text-dark font-weight-bold">Pilih Semua</span>
										</div>
										<div>
											<a href="" class="text-primary">Hapus Semua</a>
										</div>
									</div> --}}
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th scope="col">Produk</th>
													<th scope="col">Berat</th>
													<th scope="col">Harga</th>
													<th scope="col">Kuantiti</th>
													<th scope="col">Subtotal</th>
												</tr>
											</thead>
											<tbody>
												<!-- LOOPING DATA DARI VARIABLE CARTS -->
												@php $i = 1; @endphp
												@forelse ($carts as $seller)
													<input type="hidden" name="seller_id[]" value="{{ $seller['seller_id'] }}" class="form-control">
													<tr >
														<td colspan="5">
															<i class="fa fa-user mr-1"></i>
															<span class="font-weight-bold">{{ 'Penjual : ' . $seller['seller_name'] }}</span>
														</td>
													</tr>
													@foreach ($seller['products'] as $product)
														<tr>
															{{-- <td style="width: 2%;" class="text-center">
																<label class="custom-checkbox-container">
																	<input type="checkbox" checked="checked">
																	<span class="custom-checkbox"></span>
																</label>
															</td> --}}
															<td>
																<div class="media">
																	<div class="d-flex">
																		<div style="width: 100px; height: 100px; display: block; border: 1px solid #ededed; border-radius: 5px;">
																			<img src="{{ asset('/products/' . $product['product_image']) }}" style="width: 100%; height: 100%; object-fit: contain;" alt="{{ $product['product_name'] }}">
																		</div>
																	</div>
																	<div class="media-body">
																		<p class="font-weight-bold" style="color: #000;">{{ $product['product_name'] }}</p>
																		{{-- <p class="font-weight-bold" style="color: #000;"><i class="fas fa-house"></i> : {{ $seller['origin_details']['address'] }}, {{ $seller['origin_details']['city_name'] }}</p> --}}
																	</div>
																</div>
															</td>
															<td>
																@php
																	$weight = $product['weight'];
				
																	if (strpos($weight, '-') !== false) {
																		// If the weight is a range, split it into an array
																		$weights = explode('-', $weight);
																		
																		// Use the maximum value in the range for passing to controller
																		$maxWeight = (int) trim(max($weights));
				
																		$minWeight = (float) trim($weights[0]);
																		$maxWeight = (float) trim($weights[1]);
				
																		// Check if the weights are >= 1000 to display in Kg
																		$minWeightDisplay = $minWeight >= 1000 ? ($minWeight / 1000) : $minWeight;
																		$maxWeightDisplay = $maxWeight >= 1000 ? ($maxWeight / 1000) . ' Kg' : $maxWeight . ' gram / pack';
				
																		// Construct the display string
																		$weightDisplay = $minWeightDisplay . ' - ' . $maxWeightDisplay;
																	} else {
																		$maxWeight = (int) trim($weight);
				
																		// Single weight value
																		$weightDisplay = $weight >= 1000 ? ($weight / 1000) . ' Kg' : $weight . ' gram / pack';
																	}
																@endphp
																<h5>{{ $weightDisplay }}</h5>
															</td>
															<td>
																<h5>Rp {{ number_format($product['product_price'], 0, ',', '.') }}</h5>
															</td>
															{{-- <td>
																<div class="product_count">
																	<input type="text" name="qty[]" id="sst{{ $product['product_id'] }}" maxlength="12" value="{{ $product['qty'] }}" title="Quantity:" class="input-text qty">
																	<input type="hidden" name="product_id[]" value="{{ $product['product_id'] }}" class="form-control">
																	<input type="hidden" name="weight[]" value="{{ $product['weight'] }}" class="form-control">
																	<div class="ml-4">
																		<button class="increase items-count" type="button" data-id="{{ $product['product_id'] }}" data-price="{{ $product['product_price'] }}">
																			<i class="lnr lnr-chevron-up"></i>
																		</button>
																		<button class="reduced items-count" type="button" data-id="{{ $product['product_id'] }}" data-price="{{ $product['product_price'] }}">
																			<i class="lnr lnr-chevron-down"></i>
																		</button>
																	</div>
																</div>
															</td> --}}
															<td>
																<div class="product_count">
																	<button class="quantity-btn reduced" type="button" data-id="{{ $product['product_id'] }}" data-price="{{ $product['product_price'] }}">
																		<i class="fa fa-minus"></i>
																	</button>
																	<input type="text" name="qty[]" id="sst{{ $product['product_id'] }}" maxlength="12" value="{{ $product['qty'] }}" title="Quantity:" class="input-text qty">
																	<input type="hidden" name="product_id[]" value="{{ $product['product_id'] }}" class="form-control">
																	<input type="hidden" name="weight[]" value="{{ $product['weight'] }}" class="form-control">
																	<button class="quantity-btn increase" type="button" data-id="{{ $product['product_id'] }}" data-price="{{ $product['product_price'] }}">
																		<i class="fa fa-plus"></i>
																	</button>
																</div>
															</td>
															<td>
																<h5 id="price{{ $product['product_id'] }}">Rp {{ number_format($product['product_price'] * $product['qty'], 0, ',', '.') }}</h5>
															</td>
														</tr>
													@endforeach
				
													@if($seller['courier'] != null && $seller['service'] != null && $seller['shippingCost'] != null)
														<tr>
															<td>
																<span class="font-weight-bold">Pilih Kurir & Layanan : </span>
															</td>
															<td colspan="2">
																<div class="form-group mt-3">
																	<select class="form-control form-control-sm" name="courier[]" id="courier{{ $i }}" onchange="pilihkurir('{{ $i }}', {{ $maxWeight }}, {{ $seller['origin_details']['city_id'] }})">
																		<option value="jne" {{ $seller['courier'] == 'jne' ? 'selected' : '' }}>JNE</option>
																		<option value="pos" {{ $seller['courier'] == 'pos' ? 'selected' : '' }}>POS</option>
																		<option value="tiki" {{ $seller['courier'] == 'tiki' ? 'selected' : '' }}>TIKI</option>
																	</select>
																	<span class="text-danger">{{ $errors->first('courier') }}</span>
																</div>
															</td>
															<td>
																<div class="form-group mt-3">
																	<select class="form-control form-control-sm" name="service[]" id="service{{ $i }}" onchange="pilihlayanan('{{ $i }}', {{ $maxWeight  }})">
																		<option value="{{ $seller['service'] }}">{{ $seller['service'] }}</option>
																	</select>
																	<span class="text-danger">{{ $errors->first('service') }}</span>
																</div>
															</td>
															<td colspan="2">
																<div class="spanOngkir">
																	<span class="ongkir font-weight-bold" style="color: #000;" name="ongkir[]" id="ongkir{{ $i }}">Rp {{ number_format($seller['shippingCost'], 0, ',', '.') }}</span>
																</div>
															</td>
														</tr>
													@else
														<tr>
															<td>
																<span class="font-weight-bold">Pilih Kurir & Layanan : </span>
															</td>
															<td>
																<div class="form-group mt-3">
																	<select class="form-control form-control-sm" name="courier[]" id="courier{{ $i }}" onchange="pilihkurir('{{ $i }}', {{ $maxWeight  }}, {{ $seller['origin_details']['city_id'] }})">
																		<option value="">Pilih Kurir</option>
																		<option value="jne">JNE</option>
																		<option value="pos">POS</option>
																		<option value="tiki">TIKI</option>
																	</select>
																	<span class="text-danger">{{ $errors->first('courier') }}</span>
																</div>
															</td>
															<td colspan="2">
																<div class="form-group mt-3">
																	<select class="form-control form-control-sm" name="service[]" id="service{{ $i }}" onchange="pilihlayanan('{{ $i }}', {{ $maxWeight  }})">
																		<option value="">Pilih Layanan</option>
																	</select>
																	<span class="text-danger">{{ $errors->first('service') }}</span>
																</div>
															</td>
															<td>
																<div class="spanOngkir">
																	<span class="ongkir font-weight-bold" style="color: #000;" name="ongkir[]" id="ongkir{{ $i }}">Rp 0</span>
																</div>
															</td>
														</tr>
													@endif
													@php $i++; @endphp
												@empty
													<tr>
														<td colspan="6" class="text-center">Tidak ada belanjaan</td>
													</tr>
												@endforelse
											</form>
											@if($carts != null)
												{{-- <tr>
													<td colspan="4">
														<span class="float-right font-weight-bold" style="color: #000;">Subtotal :</span>
													</td> 
													<td colspan="1">
														<span class="text-center font-weight-bold" style="color: #000;" id="subtotal">Rp 0</span>
													</td>
												</tr> --}}
												<tr>
													<td colspan="4">
														<span class="float-right font-weight-bold" style="color: #000;">Total (termasuk PPn 10%) :</span>
													</td>
													<td colspan="1">
														<span class="text-center font-weight-bold" style="color: #000;" name="total" id="total">Rp 0</span>
													</td>
												</tr>
											@endif
											<tr class="out_button_area">
												<td colspan="5">
													<div class="checkout_btn_inner float-right">
														@if(Auth::guard('customer')->check())
															
															<a class="gray_btn" href="{{ route('front.product') }}">Lanjut Berbelanja</a>
															@if($carts != null)
																<a class="main_btn" id="nextPros" href="{{ route('front.shipment') }}">Proses Selanjutnya</a>
															@endif
														@else
															<a class="float-right btn btn-primary" href="{{ route('front.product') }}">Lanjut Berbelanja</a>
														@endif
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body">
							<h4 class="card-title text-dark">Ringkasan Belanja</h4>
							<div class="d-flex justify-content-between align-items center">
								<span>Subtotal</span>
								<span>:</span>
								<span id="subtotal" class="font-weight-bold text-dark"></span>
							</div>
						</div>
						<div class="card-footer">
							<div class="d-flex justify-content-between align-items-center">
								@if(Auth::guard('customer')->check())
									<a href="{{ route('front.product') }}" class="btn btn-secondary btn-sm">Lanjut Berbelanja</a>
									@if($carts != null)
										<a class="btn btn-sm btn-primary" id="nextPros" href="{{ route('front.shipment') }}">Proses Selanjutnya</a>
									@endif
								@else
									<a class="btn btn-secondary btn-sm" href="{{ route('front.product') }}">Lanjut Berbelanja</a>
								@endif
							</div>
							{{-- <div class="checkout_btn_inner float-right">
								@if(Auth::guard('customer')->check())
									
									<a href="{{ route('front.product') }}">Lanjut Berbelanja</a>
									@if($carts != null)
										<a class="main_btn" id="nextPros" href="{{ route('front.shipment') }}">Proses Selanjutnya</a>
									@endif
								@else
									<a class="float-right btn btn-primary" href="{{ route('front.product') }}">Lanjut Berbelanja</a>
								@endif
							</div> --}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Cart Area =================-->
@endsection

@section('js')
	<script>
		$(document).ready(function() {

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

            setTimeout(function() {
                $('.alert-success').remove();
                $('.alert-danger').remove();
            }, 2000);

			// auto recalculate
			// function to format currency
			function formatCurrency(amount) {
				return new Intl.NumberFormat('id-ID', {
					style: 'currency',
					currency: 'IDR',
					minimumFractionDigits: 0,
				}).format(amount);
			}

			// Function to clean and convert currency text to numeric value
			function cleanAndConvertCurrency(value) {
				return parseFloat(value.replace(/Rp\s?|[.,]/g, '').replace(/(,)/g, '.'));
			}

			// Function to calculate initial subtotal and total on page load
			function calculateInitialSubtotalAndTotal() {
				var subtotal = 0;

				// Loop through each product in the cart
				$("input[name='qty[]']").each(function(index) {
					var quantity = parseInt($(this).val());
					var price = parseFloat($(this).closest('tr').find('button[data-price]').data('price'));

					if (!isNaN(quantity) && !isNaN(price)) {
						subtotal += quantity * price;
					}
				});

				// Format and display subtotal
				var formattedSubtotal = formatCurrency(subtotal);
				$("#subtotal").text(formattedSubtotal);

				// Calculate shipping cost
				var shippingCost = 0;
				$("span[id^='ongkir']").each(function() {
					var cost = cleanAndConvertCurrency($(this).text());
					if (!isNaN(cost)) {
						shippingCost += cost;
					}
				});

				// Calculate total including 10% tax
				var total = subtotal + shippingCost + (subtotal * 0.10);

				// Format and display total
				var formattedTotal = formatCurrency(total);
				$("#total").text(formattedTotal);
			}

			// Call initial calculation on page load
			calculateInitialSubtotalAndTotal();

			$(".increase, .reduced").on("click", function() {
				var button = $(this);
				var productId = button.data("id");
				var productPrice = button.data("price");
				var inputField = $("#sst" + productId);
				var quantity = parseInt(inputField.val());

				if (button.hasClass("increase")) {
					if (!isNaN(quantity)) {
						quantity++;
					}
				} else {
					if (!isNaN(quantity) && quantity > 0) {
						quantity--;
					}
				}

				inputField.val(quantity);

				// Update the price display
				var newPrice = productPrice * quantity;
				var formattedPrice = new Intl.NumberFormat('id-ID', { 
					style: 'currency', 
					currency: 'IDR',
					minimumFractionDigits: 0,
				}).format(newPrice);
				$("#price" + productId).text(formattedPrice);

				// fungsi untuk menghapus data carts
				if(quantity === 0) {
					removeProductFromCart(productId);
				} else {
					updateSubtotalAndTotal();
				}
			});

			// Event listener for text input changes
			$(".input-text.qty").on("input", function() {
				var inputField = $(this);
				var productId = inputField.attr("id").replace("sst", ""); // Extract product ID
				var productPrice = $("button[data-id='" + productId + "']").data("price");
				var quantity = parseInt(inputField.val());

				// Allow only numbers using regex and set a valid quantity
				if (!/^\d+$/.test(inputField.val())) {
					inputField.val(inputField.val().replace(/\D/g, '')); // Remove non-digits
					quantity = parseInt(inputField.val()) || 0;
				}

				inputField.val(quantity);

				// Update the price display
				var newPrice = productPrice * quantity;
				var formattedPrice = new Intl.NumberFormat('id-ID', { 
					style: 'currency', 
					currency: 'IDR',
					minimumFractionDigits: 0,
				}).format(newPrice);
				$("#price" + productId).text(formattedPrice);

				// fungsi untuk menghapus data carts
				if(quantity === 0) {
					removeProductFromCart(productId);
				} else {
					updateSubtotalAndTotal();
				}
			});

			// function ini digunakan untuk update carts ketika quantity 0
			function removeProductFromCart(productId) {

				// Get product IDs, quantities, weights, etc.
				let arrayProductIds = [];
				$("input[name='product_id[]']").each(function(index) {
					arrayProductIds.push($(this).val());
				});

				let arrayQty = [];
				$("input[name='qty[]']").each(function(index) {
					arrayQty.push($(this).val());
				});

				let arrayWeight = [];
				$("input[name='weight[]']").each(function(index) {
					arrayWeight.push($(this).val());
				});

				let arraySellerIds = [];
				$("input[name='seller_id[]']").each(function(index) {
					arraySellerIds.push($(this).val());
				});

				// Make AJAX request to update cart
				$.ajax({
					url: '{{ route("front.update_cart") }}',
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						product_id: arrayProductIds,
						qty: arrayQty,
						weight: arrayWeight,
						seller_id: arraySellerIds,
					},
					beforeSend: function() {
						$.blockUI({ 
							message: '<i class="fa fa-spinner"></i>',
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
						$.unblockUI();
					},
					success: function(response) {
						// Remove the product row from the cart table
						if(response.success){
							$("#sst" + productId).closest('tr').remove();

							// Check if cart is empty
							if ($("input[name='qty[]']").filter(function() { 
								return $(this).val() > 0; 
							}).length === 0) {
								location.reload(true);
							}
							updateSubtotalAndTotal();

							$.toast({
								heading: 'Berhasil',
								text: 'Berhasil merubah kuantiti',
								showHideTransition: 'fade',
								icon: 'success',
								position: 'top-right',
								hideAfter: 3000
							});
							setTimeout(function () {
								window.location.reload(true);
							}, 1500)
						}
					},
					error: function(xhr) {
						var response = JSON.parse(xhr.responseText);
						if (response.error) {
							errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
						}
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
			}

			function updateSubtotalAndTotal() {
				var subtotal = 0;
				$("input[name='qty[]']").each(function(index) {
					var quantity = parseInt($(this).val());
					var price = parseFloat($(this).closest('tr').find('button[data-price]').data('price'));
					if (!isNaN(quantity) && !isNaN(price)) {
						subtotal += quantity * price;
					}
				});

				// Format subtotal and update it
				var formattedSubtotal = new Intl.NumberFormat('id-ID', {
					style: 'currency',
					currency: 'IDR',
					minimumFractionDigits: 0,
				}).format(subtotal);
				$("#subtotal").text(formattedSubtotal);

				function cleanAndConvertCurrency(value) {
					let numericValue = parseFloat(value.replace(/Rp\s?|[.,]/g, '').replace(/(,)/g, '.'));
					return isNaN(numericValue) ? 0 : numericValue;
				}

				// Calculate shipping cost
				var shippingCost = 0;
				$("span[id^='ongkir']").each(function() {
					var cost = cleanAndConvertCurrency($(this).text());
					if (!isNaN(cost)) {
						shippingCost += cost;
					}
				});

				// Calculate the total
				var total = subtotal + shippingCost + (subtotal * 0.10);

				// Format total
				var formattedTotal = new Intl.NumberFormat('id-ID', {
					style: 'currency',
					currency: 'IDR',
					minimumFractionDigits: 0,
				}).format(total);
				$("#total").text(formattedTotal);
			}

			// Function to calculate and update the total cost
			function updateTotalCost(subtotal) {
				function cleanAndConvertCurrency(value) {
					let numericValue = parseFloat(value.replace(/Rp\s?|[.,]/g, '').replace(/(,)/g, '.'));
					return isNaN(numericValue) ? 0 : numericValue;
				}

				// Calculate the shipping cost
				var shippingCost = 0;
				$("span[id^='ongkir']").each(function() {
					var cost = cleanAndConvertCurrency($(this).text());
					if (!isNaN(cost)) {
						shippingCost += cost;
					}
				});

				// Calculate the total cost
				var total = subtotal + shippingCost + (subtotal * 0.10);

				// Format and update the total cost
				var formattedTotal = new Intl.NumberFormat('id-ID', {
					style: 'currency',
					currency: 'IDR',
					minimumFractionDigits: 0,
				}).format(total);
				$("#total").text(formattedTotal);
			}
		});
		
	</script>
@endsection

@section('css')
	<style>
		/* Container around the checkbox */
		.custom-checkbox-container {
			position: relative;
			padding-left: 25px;
			cursor: pointer;
			font-size: 16px;
			user-select: none;
		}

		/* Hide the default checkbox */
		.custom-checkbox-container input[type="checkbox"] {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}

		/* Create a custom checkbox */
		.custom-checkbox {
			position: absolute;
			top: 0;
			left: 0;
			height: 20px;
			width: 20px;
			background-color: #eee;
			border-radius: 4px;
			border: 1px solid #ccc;
		}

		/* On mouse-over, add a grey background color */
		.custom-checkbox-container:hover input ~ .custom-checkbox {
			background-color: #ccc;
		}

		/* When the checkbox is checked, add a blue background */
		.custom-checkbox-container input:checked ~ .custom-checkbox {
			background-color: #007bff;
			border-color: #007bff;
		}

		/* Create the checkmark/indicator (hidden when not checked) */
		.custom-checkbox:after {
			content: "";
			position: absolute;
			display: none;
		}

		/* Show the checkmark when checked */
		.custom-checkbox-container input:checked ~ .custom-checkbox:after {
			display: block;
		}

		/* Style the checkmark/indicator */
		.custom-checkbox-container .custom-checkbox:after {
			left: 6px;
			top: 3px;
			width: 5px;
			height: 10px;
			border: solid white;
			border-width: 0 2px 2px 0;
			transform: rotate(45deg);
		}
	</style>
@endsection