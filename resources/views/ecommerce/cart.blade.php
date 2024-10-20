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
			@if (session('success'))
				<input type="hidden" id="success-message" value="{{ session('success') }}">
			@endif

			@if (session('error'))
				<input type="hidden" id="error-message" value="{{ session('error') }}">
			@endif
			
			@if($carts)
				<div class="row">
					<div class="col-lg-8">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title mt-3">Keranjang</h4>
							</div>
							<div class="card-body">
								<div class="cart_inner">
									<form action="{{ route('front.update_cart') }}" method="post">
										@csrf
										<div id="input-cost"></div>
										
										<div class="table-responsive">
											<table class="table">
												{{-- <thead>
													<tr>
														<th>Produk</th>
														<th>Harga</th>
														<th>Kuantiti</th>
														<th>Subtotal</th>
													</tr>
												</thead> --}}
												<tbody>
													@foreach ($carts as $seller)
														<input type="hidden" name="seller_id[]" value="{{ $seller['seller_id'] }}">
														<tr>
															<td colspan="5" class="font-weight-bold">
																<i class="fa fa-user mr-1"></i> Penjual: {{ $seller['seller_name'] }}
															</td>
														</tr>
														@foreach ($seller['products'] as $product)
														@php
															$weight = $product['weight'];
															if (strpos($weight, '-') !== false) {
																$weights = explode('-', $weight);
																$minWeight = (float) trim($weights[0]);
																$maxWeight = (float) trim($weights[1]);
																$weightDisplay = ($minWeight >= 1000 ? ($minWeight / 1000) . ' - ' : $minWeight . ' - ') . 
																				($maxWeight >= 1000 ? ($maxWeight / 1000) . ' Kg' : $maxWeight . ' gram / pack');
															} else {
																$weightDisplay = ($weight >= 1000 ? ($weight / 1000) . ' Kg' : $weight . ' gram / pack');
															}
														@endphp
														<tr>
															<td>
																<div class="media">
																	<div class="d-flex">
																		<img src="{{ asset('/products/' . $product['product_image']) }}" alt="{{ $product['product_name'] }}"
																			style="width: 100px; height: 100px; border-radius: 5px; object-fit: contain;">
																	</div>
																	<div class="media-body">
																		<p class="font-weight-bold">{{ $product['product_name'] }}</p>
																		<p>{{ $weightDisplay }}</p>
																	</div>
																</div>
															</td>
															<td><h5>Rp {{ number_format($product['product_price'], 0, ',', '.') }}</h5></td>
															<td>
																<div class="product_count">
																	<button class="quantity-btn reduced" type="button" data-id="{{ $product['product_id'] }}" data-price="{{ $product['product_price'] }}">
																		<i class="fa fa-minus"></i>
																	</button>
																	<input type="text" name="qty[]" id="sst{{ $product['product_id'] }}" value="{{ $product['qty'] }}" class="input-text qty">
																	<input type="hidden" name="product_id[]" value="{{ $product['product_id'] }}">
																	<input type="hidden" name="weight[]" value="{{ $product['weight'] }}">
																	<button class="quantity-btn increase" type="button" data-id="{{ $product['product_id'] }}" data-price="{{ $product['product_price'] }}">
																		<i class="fa fa-plus"></i>
																	</button>
																</div>
															</td>
															<td><h5 id="price{{ $product['product_id'] }}">Rp {{ number_format($product['product_price'] * $product['qty'], 0, ',', '.') }}</h5></td>
														</tr>
														@endforeach
													@endforeach
												</tbody>
											</table>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="card">
							<div class="card-body">
								<h4 class="card-title text-dark">Ringkasan Belanja</h4>
								<div class="d-flex justify-content-between align-items-center">
									<span>Total</span>
									<span id="subtotal" class="font-weight-bold text-dark"></span>
								</div>
							</div>
							<div class="card-footer">
								<div class="d-flex justify-content-between align-items-center">
									@if(Auth::guard('customer')->check())
										<a href="{{ route('front.product') }}" class="btn btn-secondary btn-sm">Lanjut Berbelanja</a>
										@if($carts)
											<a class="btn btn-primary btn-sm" href="{{ route('front.shipment') }}">Proses Selanjutnya</a>
										@endif
									@else
										<a class="btn btn-secondary btn-sm" href="{{ route('front.product') }}">Lanjut Berbelanja</a>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			@else
				<div class="d-flex flex-column">
					<div style="height: 200px; width: 200px; display: block; margin: 0 auto;">
						<img src="{{ asset('ecommerce/img/test-no-products.webp') }}" alt="" style="width: 100%; height: 100%; display: block; margin: 0 auto;">
					</div>
					<p class="text-center font-weight-bold" style="color: black;">Wah, keranjang belanjamu kosong</p>
					<p class="text-center font-weight-bold" style="color: black;">Yuk, isi dengan barang-barang impianmu!</p>
					<div style="padding: 0 30px;" class="text-center">
						<a href="{{ route('front.product') }}" class="btn btn-sm btn-outline-secondary"><span class="font-weight-bold" style="color: black;">Belanja Sekarang</span></a>
					</div>
				</div>
			@endif
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
					updateCarts(productId);
				} else {
					updateCarts(productId);
					updateSubtotalAndTotal();
				}
			});

			// Event listener for text input changes
			$(".input-text.qty").each(function() {
				var oldQuantity = $(this).val();

				// Handle 'input' event separately for quantity updates
				$(this).on("input", function() {
					var inputField = $(this);
					var productId = inputField.attr("id").replace("sst", ""); // Extract product ID
					var productPrice = $("button[data-id='" + productId + "']").data("price");
					var newQuantity = parseInt(inputField.val());

					// Allow only numbers using regex and set a valid quantity
					if (!/^\d+$/.test(inputField.val())) {
						inputField.val(inputField.val().replace(/\D/g, '')); // Remove non-digits
						newQuantity = parseInt(inputField.val()) || 0;
					}

					// Ensure the field shows the new quantity
					inputField.val(newQuantity);

					// Update the price display
					var newPrice = productPrice * newQuantity;
					var formattedPrice = new Intl.NumberFormat('id-ID', { 
						style: 'currency', 
						currency: 'IDR',
						minimumFractionDigits: 0,
					}).format(newPrice);
					$("#price" + productId).text(formattedPrice);

					// If the quantity has changed, update the cart
					if (newQuantity !== parseInt(oldQuantity)) {
						if (newQuantity > oldQuantity) {
							updateCarts(productId);  
						}
						oldQuantity = newQuantity; 
					}

					// Ensure cart update if quantity becomes 0
					if (newQuantity === 0) {
						updateCarts(productId);
					}

					updateSubtotalAndTotal();  // Update the subtotal and total calculations
				});

				// Handle 'keydown' event for Backspace and Delete keys
				$(this).on("keydown", function(e) {
					var inputField = $(this);
					var productId = inputField.attr("id").replace("sst", ""); // Extract product ID

					// Check if 'Backspace' or 'Delete' is pressed
					if (e.key === "Backspace" || e.key === "Delete") {
						var newQuantity = parseInt(inputField.val()) || 0;

						inputField.val(newQuantity);

						// If the quantity becomes 0 after backspace/delete
						if (newQuantity === 0) {
							updateCarts(productId);
						}
					}
				});
			});


			function updateCarts(productId) {
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
                        $('.cart_inner').block({ 
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
                        $('.cart_inner').unblock();
                    },
					success: function(response) {
						// Remove the product row from the cart table if quantity is 0
						if (response.success) {
							let currentQty = parseInt($("#sst" + productId).val());
							console.log(currentQty);
							
							if (currentQty === 0) {
								$("#sst" + productId).closest('tr').remove();
							}

							// Check if cart is empty
							if ($("input[name='qty[]']").filter(function() { 
								return $(this).val() > 0; 
							}).length === 0) {
								$.toast({
									heading: 'Berhasil',
									text: 'Berhasil menghapus produk',
									showHideTransition: 'fade',
									icon: 'success',
									position: 'top-right',
									hideAfter: 2000
								});
								setTimeout(function() {
									window.location.reload(true)
								}, 1500);
							} else {
								$.toast({
									heading: 'Berhasil',
									text: response.success,
									showHideTransition: 'fade',
									icon: 'success',
									position: 'top-right',
									hideAfter: 2000
								});
							}

							updateSubtotalAndTotal();
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
						setTimeout(function() {
							window.location.reload(true);
						}, 1500);
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