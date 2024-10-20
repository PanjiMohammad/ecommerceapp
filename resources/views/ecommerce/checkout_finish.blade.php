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
					<h2>Pesanan Diterima</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
						<a href="">Faktur</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Order Details Area =================-->
	<section class="order_details p_100">
		<div class="container">
			@if (session('success'))
				<input type="hidden" id="success-message" value="{{ session('success') }}">
			@endif

			@if (session('error'))
				<input type="hidden" id="error-message" value="{{ session('error') }}">
			@endif

			<h3 class="title_confirmation">Terima kasih {{ $order->customer_name }}, pesanan anda telah kami terima.</h3>
			<p class="title_timer_countdown timer" id="countdown"></p>
			{{-- <div id="timer" class="timer text-center" style="margin-bottom: 30px;">
				<span class="text-danger font-weight-bold title_timer_countdown">Pesanan anda akan berakhir dalam</span>
				<span id="hours" class="time-box title_timer_countdown">00</span> :
				<span id="minutes" class="time-box title_timer_countdown">00</span> :
				<span id="seconds" class="time-box title_timer_countdown">00</span>
			</div> --}}
			<div>
				<div class="py-4">
				  	<div class="py-6">
						<table class="w-full border-collapse border-spacing-0">
					  		<tbody>
								<tr>
						  			<td class="w-full align-top">
										<div>
											<img src="{{ asset('ecommerce/img/logo-psj.jpg') }}" style="height: 90px; margin-top: -50px;"/>
										</div>
									</td>
			
									<td class="align-top">
										<div class="text-sm">
											<table class="border-collapse border-spacing-0">
												<tbody>
													<tr>
														<td class="border-r pr-4">
															<div>
																<p class="whitespace-nowrap text-slate-400 text-right">Tanggal :</p>
																<p class="whitespace-nowrap font-bold text-main text-right">{{ $formattedDate }}</p>
															</div>
														</td>
														<td class="border-r pr-4 pl-4">
															<div>
																<p class="whitespace-nowrap text-slate-400 text-right">Invoice :</p>
																<p class="whitespace-nowrap font-bold text-main text-right" style="text-transform: uppercase">{{ $order->invoice }}</p>
															</div>
														</td>
														<td class="pl-4">
															<div>
																<p class="whitespace-nowrap text-slate-400 text-right">Status Pembayaran :</p>
																<p class="whitespace-nowrap font-bold text-main text-right">{!! $order->payment->status_label_new ?? '-' !!}</p>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</td>
								</tr>
					  		</tbody>
						</table>
					</div>
			
					<div class="bg-slate-100 px-14 py-6 text-sm">
						<table class="w-full border-collapse border-spacing-0">
							<tbody>
								<tr>
									<td class="w-1/2 align-top">
										<div class="text-sm text-neutral-600">
											<p class="font-bold text-uppercase">Penjual</p>
											<div class="mt-2">
												@foreach($transformedOrder['sellers'] as $index => $seller)
													@if(count($seller) > 1)
														<p><strong>Penjual {{ $index + 1 }} :</strong> {{ $seller['name'] . ' (' . $seller['phone'] . ')' }}</p>
														<br>
													@else
														<p><strong>Penjual :</strong> {{ $seller['name'] . ' (' . $seller['phone'] . ')' }}</p>
														<br>
													@endif
												@endforeach
											</div>
										</div>
									</td>
									<td class="w-1/2 align-top text-right">
										<div class="text-sm text-neutral-600">
											<p class="font-bold text-uppercase">Penerima</p>
											<div class="mt-2">
												<p><strong>Nama :</strong> {{ $order->customer_name . ' (' . $order->customer_phone . ')' }}</p>
												<p><strong>Alamat :</strong> {{ $order->customer->address . ',' }}</p>
												<p>{{ 'Kecamatan ' . $order->customer->district->name }}, {{ 'Kota ' . $order->customer->district->city->name . ',' }}</p>
                                    			<p>{{ $order->customer->district->city->province->name . ', Kode Pos ' . $order->customer->district->city->postal_code }}</p>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
			
					<div class="py-10 text-sm text-neutral-700">
						<table class="w-full border-collapse border-spacing-0">
							<thead>
								<tr>
									<td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">#</td>
									<td colspan="2" class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Produk</td>
									<td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Berat</td>
									<td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Harga</td>
									<td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Kuantiti</td>
									<td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Subtotal</td>
									<td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Ongkos Kirim</td>
									<td class="border-b-2 border-main pb-3 pl-2 pr-3 font-bold text-main">Kurir</td>
								</tr>
							</thead>
							<tbody>
								@foreach ($order->details as $row)
									<tr>
										<td class="border-b py-3 pl-2">{{ $loop->iteration . '.' }}</td>
										<td class="border-b py-3 pl-2" style="width: 10%;">
											<div style="height: 70px; width: 70px; display: block; border: 1px solid transparent;">
												<img src="{{ asset('/products/' . $row->product->image) }}" alt="{{ $row->product->name }}" style="border-radius: 4px; height: 100%; width: 100%; object-fit: contain; display: block;">
											</div>
										</td>
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

											$services = $row->shipping_service;
											$parts = explode(' - ', $services);
										@endphp
										<td class="border-b py-3 pl-2">
											<span class="font-weight-bold">{{ $row->product->name }}</span>
										</td>
										<td class="border-b py-3 pl-2">{{ $weightDisplay }}</td>
										<td class="border-b py-3 pl-2">Rp {{ number_format(($row->promo_price ?? $row->price), 0, ',', '.') }}</td>
										<td class="border-b py-3 pl-2">{{ $row->qty }} Item</td>
										<td class="border-b py-3 pl-2">Rp {{ number_format(($row->promo_price ?? $row->price) * $row->qty, 0, ',', '.') }}</td>
										<td class="border-b py-3 pl-2">Rp {{ number_format($row->shipping_cost, 0, ',', '.') }}</td>
										<td class="border-b py-3 pl-2">{{ $parts[0] . ' - ' . $parts[1] }}</td>
									</tr>
								@endforeach
								<tr>
									<td colspan="9">
										<table class="w-full border-collapse border-spacing-0">
											<tbody>
												<tr>
													<td class="w-full"></td>
													<td>
														<table class="w-full border-collapse border-spacing-0">
															<tbody>
																<tr>
																	<td class="border-b p-3">
																		<div class="whitespace-nowrap text-slate-400">Subtotal</div>
																	</td>
																	<td class="border-b p-3 text-right">
																		<div class="whitespace-nowrap font-bold text-main">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</div>
																	</td>
																</tr>
																<tr>
																	<td class="border-b p-3">
																		<div class="whitespace-nowrap text-slate-400">Ongkos Kirim</div>
																	</td>
																	<td class="border-b p-3 text-right">
																		<div class="whitespace-nowrap font-bold text-main">Rp {{ number_format($order->cost, 0, ',', '.') }}</div>
																	</td>
																</tr>
																<tr>
																	<td class="border-b p-3">
																		<div class="whitespace-nowrap text-slate-400">Biaya Layanan</div>
																	</td>
																	<td class="border-b p-3 text-right">
																		<div class="whitespace-nowrap font-bold text-main">Rp {{ number_format($order->service_cost, 0, ',', '.') }}</div>
																	</td>
																</tr>
																<tr>
																	<td class="border-b p-3">
																		<div class="whitespace-nowrap text-slate-400">Biaya Kemasan</div>
																	</td>
																	<td class="border-b p-3 text-right">
																		<div class="whitespace-nowrap font-bold text-main">Rp {{ number_format($order->packaging_cost, 0, ',', '.') }}</div>
																	</td>
																</tr>
																
																<tr>
																	<td class="bg-main p-3">
																		<div class="whitespace-nowrap font-bold text-white">Total Pembayaran</div>
																	</td>
																	<td class="bg-main p-3 text-right">
																		<div class="whitespace-nowrap font-bold text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<hr class="mb-3">
			<div class="float-right">
				@if(auth()->guard('customer')->check())
					<button class="cancel_btn btn btn-md mr-1 cancel-button">Batalkan Pesanan</button>
					<a class="main_btn btn btn-md bg-main" href="{{ route('customer.view_order', $order->invoice) }}">Lihat Pesanan</a>
				@else
					<h3 class="title_confirmation text-center">Silahkan Periksa Email Anda Untuk Verifikasi</h3>
				@endif
			</div>
		</div>
	</section>
	<!--================End Order Details Area =================-->

	<!-- Lihat Invoice Modal -->
	<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" style="max-width: 80%;" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title font-bold">Form Pembatalan Pesanan</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="cancelOrderForm" action="{{ route('customer.cancel') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="invoice" id="invoice" value="{{ $order->invoice }}">
					<input type="hidden" name="_method" value="PUT">
					<div class="modal-body loader-area-cancel-modal">
						<p class="mb-3 font-weight-bold">Silakan pilih alasan pembatalan : </p>
						<div id="cancel-reason-group" class="cancel-reason-group">
							<div class="form-check form-group">
								<input class="form-check-input" type="radio" name="cancel_reason" id="reason1" value="Saya berubah pikiran">
								<label class="form-check-label" for="reason1">
									Saya berubah pikiran
								</label>
							</div>
							<div class="form-check form-group">
								<input class="form-check-input" type="radio" name="cancel_reason" id="reason2" value="Barang terlalu lama dikirim">
								<label class="form-check-label" for="reason2">
									Barang terlalu lama dikirim
								</label>
							</div>
							<div class="form-check form-group">
								<input class="form-check-input" type="radio" name="cancel_reason" id="reason3" value="Pesanan tidak sesuai">
								<label class="form-check-label" for="reason3">
									Pesanan tidak sesuai
								</label>
							</div>
							<div class="form-check form-group">
								<input class="form-check-input" type="radio" name="cancel_reason" id="reason4" value="Ada masalah dengan metode pembayaran">
								<label class="form-check-label" for="reason4">
									Ada masalah dengan metode pembayaran
								</label>
							</div>
							<div class="form-check form-group">
								<input class="form-check-input" type="radio" name="cancel_reason" id="reason5" value="Alasan lain">
								<label class="form-check-label" for="reason5">
									Alasan lain
								</label>
							</div>
						</div>
						<span class="text-danger" id="cancel_reason_error"></span>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-outline-danger btn-sm">Kirim</button>
					</div>
				</form>
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

			// modal invoice
			$('.cancel-button').on('click', function(){
				$('#cancelModal').modal('show');
			})

			// submit ajax
			$('#cancelOrderForm').submit(function (e) {
				e.preventDefault();

				$('#cancel_reason_error').text('');
				$('.form-check').removeClass('input-error');

				var formData = new FormData(this);

				$.ajax({
					url: "{{ route('customer.cancel') }}",
					type: "POST",
					data: formData,
					processData: false, 
					contentType: false,
					beforeSend: function() {
						$('#cancel_reason_error').text('');
						$('.form-check').removeClass('input-error');
                        $('.loader-area-cancel-modal').block({ 
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
                        $('.loader-area-cancel-modal').unblock();
                    },
					success: function (response) {
						$('#cancel_reason_error').text('');
						$('.form-check').removeClass('input-error');
						$('#cancelOrderModal').modal('hide');
						if(response.success) {
							$.toast({
								heading: 'Berhasil',
								text: response.success,
								showHideTransition: 'fade',
								icon: 'success',
								position: 'top-right',
								hideAfter: 3000
							});
							setTimeout(function() {
								window.location.href = response.redirect;
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
					error: function (xhr, status, error) {
						$('#cancelOrderModal').modal('hide');

						var response = JSON.parse(xhr.responseText);
						if (response.error) {
							var errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;

							$.toast({
								heading: 'Gagal',
								text: errorMessage,
								showHideTransition: 'fade',
								icon: 'error',
								position: 'top-right',
								hideAfter: 3000
							});
							setInterval(function() {
								// Show error message
								$('#cancel_reason_error').text(response.errors.cancel_reason[0]);

								// Add error class to the radio button group container
								$('#cancel-reason-group').addClass('input-error');
							}, 1000);
						} else {
							// Generic error message if no specific error is returned
							errorMessage = 'Terjadi kesalahan pada server.';
							$.toast({
								heading: 'Gagal',
								text: errorMessage,
								showHideTransition: 'fade',
								icon: 'error',
								position: 'top-right',
								hideAfter: 3000
							});
						}
					}
				});
			});

			// set timer
			// Set the order created date
			var createdDate = new Date('{{ $order->created_at }}');
    
			// Add 24 hours to the created date
			var countdownDate = new Date(createdDate.getTime() + 24 * 60 * 60 * 1000);

			// Update the count down every 1 second
			var x = setInterval(function() {

				// Get today's date and time
				var now = new Date().getTime();

				// Find the distance between now and the countdown date
				var distance = countdownDate - now;

				// Time calculations for days, hours, minutes and seconds
				var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
				var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
				var seconds = Math.floor((distance % (1000)) / 1000);

				$('#hours').text(("0" + hours).slice(-2));
				$('#minutes').text(("0" + minutes).slice(-2));
				$('#seconds').text(("0" + seconds).slice(-2));

				// Display the result in the element with id="countdown"
				// $('#countdown').html('Waktu tersisa: ' + hours + ' Jam ' + minutes + ' Menit ' + seconds + ' Detik ');

				// If the countdown is over, write some text 
				if (distance < 0) {
					clearInterval(x);
					$('#hours').text("00");
					$('#minutes').text("00");
					$('#seconds').text("00");
					$('#countdown').html('Countdown selesai');
				}
			}, 1000);

		});
	</script>
@endsection

@section('css')
	<style>
		*, ::before, ::after {
			box-sizing: border-box;
			/* 1 */
			border-width: 0;
			/* 2 */
			border-style: solid;
			/* 2 */
			border-color: #e5e7eb;
			/* 2 */
        }

        ::before, ::after {
        	--tw-content: '';
        }

        /*
        1. Use a consistent sensible line-height in all browsers.
        2. Prevent adjustments of font size after orientation changes in iOS.
        3. Use a more readable tab size.
        4. Use the user's configured `sans` font-family by default.
        5. Use the user's configured `sans` font-feature-settings by default.
        6. Use the user's configured `sans` font-variation-settings by default.
        */

        html {
			line-height: 1.5;
			/* 1 */
			-webkit-text-size-adjust: 100%;
			/* 2 */
			-moz-tab-size: 4;
			/* 3 */
			tab-size: 4;
			/* 3 */
			font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
			/* 4 */
			font-feature-settings: normal;
			/* 5 */
			font-variation-settings: normal;
			/* 6 */
        }

        /*
        1. Remove the margin in all browsers.
        2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
        */

        body {
			margin: 0;
			/* 1 */
			line-height: inherit;
			/* 2 */
        }

        /*
        1. Add the correct height in Firefox.
        2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
        3. Ensure horizontal rules are visible by default.
        */

        hr {
			height: 0;
			/* 1 */
			color: inherit;
			/* 2 */
			border-top-width: 1px;
			/* 3 */
        }

        /*
        Add the correct text decoration in Chrome, Edge, and Safari.
        */

        abbr:where([title]) {
			-webkit-text-decoration: underline dotted;
					text-decoration: underline dotted;
        }

        /*
        Remove the default font size and weight for headings.
        */

        h1, h2, h3, h4, h5, h6 {
			font-size: inherit;
			font-weight: inherit;
        }

        /*
        Reset links to optimize for opt-in styling instead of opt-out.
        */

        a {
			color: inherit;
			text-decoration: inherit;
        }

        /*
        Add the correct font weight in Edge and Safari.
        */

        b, strong {
        	font-weight: bolder;
        }

        /*
        1. Use the user's configured `mono` font family by default.
        2. Correct the odd `em` font sizing in all browsers.
        */

        code, kbd, samp, pre {
			font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
			/* 1 */
			font-size: 1em;
			/* 2 */
        }

        /*
        Add the correct font size in all browsers.
        */

        small {
        	font-size: 80%;
        }

        /*
        Prevent `sub` and `sup` elements from affecting the line height in all browsers.
        */

        sub, sup {
			font-size: 75%;
			line-height: 0;
			position: relative;
			vertical-align: baseline;
        }

        sub {
        	bottom: -0.25em;
        }

        sup {
        	top: -0.5em;
        }

        /*
        1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
        2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
        3. Remove gaps between table borders by default.
        */

        table {
			text-indent: 0;
			/* 1 */
			border-color: inherit;
			/* 2 */
			border-collapse: collapse;
			/* 3 */
        }

        /*
        1. Change the font styles in all browsers.
        2. Remove the margin in Firefox and Safari.
        3. Remove default padding in all browsers.
        */

        button, input, optgroup, select, textarea {
			font-family: inherit;
			/* 1 */
			font-feature-settings: inherit;
			/* 1 */
			font-variation-settings: inherit;
			/* 1 */
			font-size: 100%;
			/* 1 */
			font-weight: inherit;
			/* 1 */
			line-height: inherit;
			/* 1 */
			color: inherit;
			/* 1 */
			margin: 0;
			/* 2 */
			padding: 0;
			/* 3 */
        }

        /*
        Remove the inheritance of text transform in Edge and Firefox.
        */

        button, select {
        	text-transform: none;
        }

        /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Remove default button styles.
        */

        button, [type='button'], [type='reset'], [type='submit'] {
			-webkit-appearance: button;
			/* 1 */
			background-color: transparent;
			/* 2 */
			background-image: none;
			/* 2 */
        }

        /*
        Use the modern Firefox focus style for all focusable elements.
        */

        :-moz-focusring {
        	outline: auto;
        }

        /*
        Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
        */

        :-moz-ui-invalid {
        	box-shadow: none;
        }

        /*
        Add the correct vertical alignment in Chrome and Firefox.
        */

        progress {
        	vertical-align: baseline;
        }

        /*
        Correct the cursor style of increment and decrement buttons in Safari.
        */

        ::-webkit-inner-spin-button, ::-webkit-outer-spin-button {
        	height: auto;
        }

        /*
        1. Correct the odd appearance in Chrome and Safari.
        2. Correct the outline style in Safari.
        */

        [type='search'] {
			-webkit-appearance: textfield;
			/* 1 */
			outline-offset: -2px;
			/* 2 */
        }

        /*
        Remove the inner padding in Chrome and Safari on macOS.
        */

        ::-webkit-search-decoration {
        	-webkit-appearance: none;
        }

        /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Change font properties to `inherit` in Safari.
        */

        ::-webkit-file-upload-button {
			-webkit-appearance: button;
			/* 1 */
			font: inherit;
			/* 2 */
        }

        /*
        Add the correct display in Chrome and Safari.
        */

        summary {
        	display: list-item;
        }

        /*
        Removes the default spacing and border for appropriate elements.
        */

        blockquote, dl, dd, h1, h2, h3, h4, h5, h6, hr, figure, p, pre {
        	margin: 0;
        }

        fieldset {
			margin: 0;
			padding: 0;
        }

        legend {
        	padding: 0;
        }

        ol, ul, menu {
			list-style: none;
			margin: 0;
			padding: 0;
        }

        /*
        Reset default styling for dialogs.
        */

        dialog {
        	padding: 0;
        }

        /*
        Prevent resizing textareas horizontally by default.
        */

        textarea {
        	resize: vertical;
        }

        /*
        1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
        2. Set the default placeholder color to the user's configured gray 400 color.
        */

        input::placeholder, textarea::placeholder {
			opacity: 1;
			/* 1 */
			color: #9ca3af;
			/* 2 */
        }

        /*
        Set the default cursor for buttons.
        */

        button,
        [role="button"] {
        	cursor: pointer;
        }

        /*
        Make sure disabled buttons don't get the pointer cursor.
        */

        :disabled {
        	cursor: default;
        }

        /*
        1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
        2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
        This can trigger a poorly considered lint error in some tools but is included by design.
        */

        img, svg, video, canvas, audio, iframe, embed, object {
			display: block;
			/* 1 */
			vertical-align: middle;
			/* 2 */
        }

        /*
        Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
        */

        img, video {
			max-width: 100%;
			height: auto;
        }

        /* Make elements with the HTML hidden attribute stay hidden by default */

        [hidden] {
        	display: none;
        }

        *, ::before, ::after{
			--tw-border-spacing-x: 0;
			--tw-border-spacing-y: 0;
			--tw-translate-x: 0;
			--tw-translate-y: 0;
			--tw-rotate: 0;
			--tw-skew-x: 0;
			--tw-skew-y: 0;
			--tw-scale-x: 1;
			--tw-scale-y: 1;
			--tw-pan-x:  ;
			--tw-pan-y:  ;
			--tw-pinch-zoom:  ;
			--tw-scroll-snap-strictness: proximity;
			--tw-gradient-from-position:  ;
			--tw-gradient-via-position:  ;
			--tw-gradient-to-position:  ;
			--tw-ordinal:  ;
			--tw-slashed-zero:  ;
			--tw-numeric-figure:  ;
			--tw-numeric-spacing:  ;
			--tw-numeric-fraction:  ;
			--tw-ring-inset:  ;
			--tw-ring-offset-width: 0px;
			--tw-ring-offset-color: #fff;
			--tw-ring-color: rgb(59 130 246 / 0.5);
			--tw-ring-offset-shadow: 0 0 #0000;
			--tw-ring-shadow: 0 0 #0000;
			--tw-shadow: 0 0 #0000;
			--tw-shadow-colored: 0 0 #0000;
			--tw-blur:  ;
			--tw-brightness:  ;
			--tw-contrast:  ;
			--tw-grayscale:  ;
			--tw-hue-rotate:  ;
			--tw-invert:  ;
			--tw-saturate:  ;
			--tw-sepia:  ;
			--tw-drop-shadow:  ;
			--tw-backdrop-blur:  ;
			--tw-backdrop-brightness:  ;
			--tw-backdrop-contrast:  ;
			--tw-backdrop-grayscale:  ;
			--tw-backdrop-hue-rotate:  ;
			--tw-backdrop-invert:  ;
			--tw-backdrop-opacity:  ;
			--tw-backdrop-saturate:  ;
			--tw-backdrop-sepia:  ;
        }

        ::backdrop{
			--tw-border-spacing-x: 0;
			--tw-border-spacing-y: 0;
			--tw-translate-x: 0;
			--tw-translate-y: 0;
			--tw-rotate: 0;
			--tw-skew-x: 0;
			--tw-skew-y: 0;
			--tw-scale-x: 1;
			--tw-scale-y: 1;
			--tw-pan-x:  ;
			--tw-pan-y:  ;
			--tw-pinch-zoom:  ;
			--tw-scroll-snap-strictness: proximity;
			--tw-gradient-from-position:  ;
			--tw-gradient-via-position:  ;
			--tw-gradient-to-position:  ;
			--tw-ordinal:  ;
			--tw-slashed-zero:  ;
			--tw-numeric-figure:  ;
			--tw-numeric-spacing:  ;
			--tw-numeric-fraction:  ;
			--tw-ring-inset:  ;
			--tw-ring-offset-width: 0px;
			--tw-ring-offset-color: #fff;
			--tw-ring-color: rgb(59 130 246 / 0.5);
			--tw-ring-offset-shadow: 0 0 #0000;
			--tw-ring-shadow: 0 0 #0000;
			--tw-shadow: 0 0 #0000;
			--tw-shadow-colored: 0 0 #0000;
			--tw-blur:  ;
			--tw-brightness:  ;
			--tw-contrast:  ;
			--tw-grayscale:  ;
			--tw-hue-rotate:  ;
			--tw-invert:  ;
			--tw-saturate:  ;
			--tw-sepia:  ;
			--tw-drop-shadow:  ;
			--tw-backdrop-blur:  ;
			--tw-backdrop-brightness:  ;
			--tw-backdrop-contrast:  ;
			--tw-backdrop-grayscale:  ;
			--tw-backdrop-hue-rotate:  ;
			--tw-backdrop-invert:  ;
			--tw-backdrop-opacity:  ;
			--tw-backdrop-saturate:  ;
			--tw-backdrop-sepia:  ;
        }

        .fixed{
        	position: fixed;
        }

        .bottom-0{
        	bottom: 0px;
        }

        .left-0{
        	left: 0px;
        }

        .table{
        	display: table;
        }

        .h-12{
        	height: 3rem;
        }

        .w-1\/2{
        	width: 50%;
        }

        .w-full{
        	width: 100%;
        }

        .border-collapse{
        	border-collapse: collapse;
        }

        .border-spacing-0{
        	--tw-border-spacing-x: 0px;
        	--tw-border-spacing-y: 0px;
        	border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
        }

        .whitespace-nowrap{
        	white-space: nowrap;
        }

        .border-b{
        	border-bottom-width: 1px;
        }

        .border-b-2{
        	border-bottom-width: 2px;
        }

        .border-r{
        	border-right-width: 1px;
        }

        .border-main{
        	border-color: #5c6ac4;
        }

        .bg-main{
        	background-color: #5c6ac4;
        }

        .bg-slate-100{
        	background-color: #f1f5f9;
        }

        .p-3{
        	padding: 0.75rem;
        }

        .px-14{
        	padding-left: 3.5rem;
        	padding-right: 3.5rem;
        }

        .px-2{
        	padding-left: 0.5rem;
        	padding-right: 0.5rem;
        }

        .py-10{
        	padding-top: 2.5rem;
        	padding-bottom: 2.5rem;
        }

        .py-3{
        	padding-top: 0.75rem;
        	padding-bottom: 0.75rem;
        }

        .py-4{
       		padding-top: 1rem;
        	padding-bottom: 1rem;
        }

        .py-6{
        	padding-top: 1.5rem;
        	padding-bottom: 1.5rem;
        }

        .pb-3{
        	padding-bottom: 0.75rem;
        }

        .pl-2{
        	padding-left: 0.5rem;
        }

        .pl-3{
        	padding-left: 0.75rem;
        }

        .pl-4{
        	padding-left: 1rem;
        }

        .pr-3{
        	padding-right: 0.75rem;
        }

        .pr-4{
        	padding-right: 1rem;
        }

        .text-center{
        	text-align: center;
        }

        .text-right{
        	text-align: right;
        }

        .align-top{
        	vertical-align: top;
        }

        .text-sm{
        	font-size: 0.875rem;
        	line-height: 1.25rem;
        }

        .text-xs{
        	font-size: 0.75rem;
        	line-height: 1rem;
        }

        .font-bold{
        	font-weight: 700;
        }

        .italic{
        	font-style: italic;
        }

        .text-main{
        	color: #5c6ac4;
        }

        .text-neutral-600{
        	color: #525252;
        }

        .text-neutral-700{
        	color: #404040;
        }

        .text-slate-300{
        	: #cbd5e1;
        }

        .text-slate-400{
            color: #94a3b8;
        }

        .text-white{
            color: #fff;
        }

		.input-error {
			border: 2px solid red;
			padding: 10px;
			border-radius: 5px;
		}

        @page {
            margin: 0;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
	</style>
@endsection