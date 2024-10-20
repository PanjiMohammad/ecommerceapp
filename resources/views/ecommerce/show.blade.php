@extends('layouts.ecommerce')

@section('title')
    <title>Jual {{ $product->name }}</title>
@endsection

@section('orderwa')
<div class="floatwa">
	<a href="https://api.whatsapp.com/send?phone=6287889165715&amp;text=Halo%20gan,%20Saya%20mau%20order {{ $product->name }}" target="_blank"><i class="fa-brands fa-whatsapp tombolwa"></i></a>
</div>

@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
                    <h2>{{ $product->name }}</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <i style="font-style: normal; color: black">{{ $product->name }}</i>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<div class="product_image_area">
		<div class="container">
			@if (session('success'))
                <input type="hidden" id="success-message" value="{{ session('success') }}">
            @endif

            @if (session('error'))
                <input type="hidden" id="error-message" value="{{ session('error') }}">
            @endif
			<div class="row s_product_inner">
				<div class="col-lg-6">
					<div class="s_product_img" style="border: 1px solid #ededed;">
						<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
							<div class="carousel-inner">
								<div class="carousel-item active">
									<img class="d-block w-100 img-fluid rounded mh-100 mx-auto" style="object-fit: contain;" src="{{ asset('/products/' . $product->image) }}" alt="{{ $product->name }}">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-5 offset-lg-1">
					<div class="s_product_text">
						<h3>{{ $product->name }}</h3>
						<h2 class="harga">{{ 'Rp ' . number_format(($product->promo_price === null || $product->promo_price == 0 ? $product->price : $product->promo_price), 0, ',', '.') }}</h2>
                        @if($product->promo_price !== null && $product->promo_price !== 0 )
							<span class="badge badge-danger" style="font-size: 14px;">{{ round((($product->price - $product->promo_price) / $product->price) * 100) . '%' }}</span>
							<span class="ml-1" style="text-decoration: line-through;">{{ 'Rp ' . number_format($product->price, 0, ',', '.') }}</span>
						@endif
						<hr>
						<!-- TAMBAHKAN FORM ACTION -->
						{{-- <form action="{{ route('front.cart') }}" method="POST">
						@csrf
							<div class="product_count">
								<label for="qty">Kuantiti:</label>
								<input type="text" name="qty" id="sst" maxlength="12" value="1" title="Quantity:" class="input-text qty">
								
								<!-- BUAT INPUTAN HIDDEN YANG BERISI ID PRODUK -->
								<input type="hidden" name="product_id" value="{{ $product->id }}" class="form-control">
								<input type="hidden" name="seller_id" value="{{ $product->seller_id }}" class="form-control">
								<input type="hidden" name="seller_name" value="{{ $product->seller_name }}" class="form-control">
								
								<button onclick="increaseQty(); return false;" class="increase items-count" type="button">
									<i class="lnr lnr-chevron-up"></i>
								</button>
								<button onclick="decreaseQty(); return false;" class="reduced items-count" type="button">
									<i class="lnr lnr-chevron-down"></i>
								</button>
							</div>
							<div class="card_area">
								<button class="main_btn">Tambah ke Keranjang <i class="fas fa-dolly"></i></button>
							</div>
						</form> --}}

						<form id="add-to-cart-form" method="POST">
							@csrf
							<div class="product_count align-items-center d-flex">
								<label for="qty" class="mt-2 mr-3">Kuantiti:</label>
								<button onclick="decreaseQty(); return false;" class="reduced items-count" type="button">
									<i class="fa fa-minus"></i>
								</button>
								<input type="text" name="qty" id="sst" maxlength="12" value="1" title="Quantity:" class="input-text qty">
								<!-- BUAT INPUTAN HIDDEN YANG BERISI ID PRODUK -->
								<input type="hidden" name="product_id" value="{{ $product->id }}" class="form-control">
								<input type="hidden" name="seller_id" value="{{ $product->seller_id }}" class="form-control">
								<input type="hidden" name="seller_name" value="{{ $product->seller_name }}" class="form-control">
								<button onclick="increaseQty(); return false;" class="increase items-count" type="button">
									<i class="fa fa-plus"></i>
								</button>
							</div>
						</form>
							<div class="d-flex">
								<button class="main-button" id="addToCart" title="Tambah Ke Keranjang">Tambah ke Keranjang <i class="lnr lnr lnr-cart" style="font-size: 14px;"></i></button>
								
								@if(auth()->guard('customer')->check())
									@if($wishlist != NULL && $product->id == $wishlist->product_id)
										<form id="delete-wishlist-form-{{ $wishlist->id }}" action="{{ route('customer.deleteWishlist', $wishlist->id) }}" method="POST">
											@csrf
											@method('DELETE')
											<button type="button" class="grey-button ml-2 delete-wishlist" data-wishlist-id="{{ $wishlist->id }}" data-product-id="{{ $product->id }}">
												Hapus dari daftar keinginan <i class="fas fa-trash" style="font-size: 14px;"></i>
											</button>
										</form>
									@else
										<form id="add-wishlist-form-{{ $product->id }}" action="{{ route('customer.save_wishlist') }}" method="POST">
											@csrf
											<input type="hidden" name="product_id" value="{{ $product->id }}" class="form-control">
											<button class="main-button ml-2 add-wishlist" data-product-id="{{ $product->id }}" title="Tambah Ke Daftar Keinginan">
												Tambah ke Daftar Keingan <i class="fa-regular fa-heart" style="font-size: 14px;"></i>
											</button>
										</form>
									@endif
								@endif
							</div>

						{{-- @if (session('success'))
							<div class="alert alert-success mt-2">{{ session('success') }}</div>
						@elseif(session('error'))
							<div class="alert alert-danger mt-2">{{ session('error') }}</div>
						@endif --}}
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--================End Single Product Area =================-->

	<!--================Product Description Area =================-->
	<section class="product_description_area">
		<div class="container">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active show" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Deskripsi</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Spesifikasi</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="rating-tab" data-toggle="tab" href="#rating" role="tab" aria-controls="rating" aria-selected="false">Ulasan</a>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<!-- Deskripsi -->
				<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab" style="color: black">
					{{-- <p style="font-size: 16px;">
						<i class="fa fa-star mr-1" aria-hidden="true"></i>
						<span class="font-weight-bold">{{ $product->ratings->avg('rating') ?? 0 }} / 5</span>
						<span class="ml-1">({{ $product->ratings->count('comment') ?? 0 }} ulasan)</span>
					</p> --}}
					{!! $product->description !!}
				</div>
				<!-- Spesifikasi -->
				<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
					{{-- <p>Total Ratings: {{ $product->ratings->count() }}</p> --}}
					<div class="table-responsive">
						<table class="table table-hover table-borderless">
							<tbody>
								<tr>
									<td>
										<span style="color: #000;">Penjual</span>
									</td>
									<td><span style="color: #000;">:</span></td>
									<td>
										<span style="color: #000;">{{ $product->seller_name }}</span>
									</td>
								</tr>
								<tr>
									<td><span style="color: #000;">Berat</span></td>
									<td><span style="color: #000;">:</span></td>
									<td><span style="color: #000;">{{ $product->weight }} gram / pack</span></td>
								</tr>
								<tr>
									<td>
										<span style="color: #000;">Harga</span>
									</td>
									<td><span style="color: #000;">:</span></td>
									<td>
										@if($product->promo_price == null && $product->promo_price == 0)
											<span style="color: #000;">{{ 'Rp ' . number_format($product->price, 0, ',', '.') }}</span>
										@else
											<span style="color: #000;">{{ 'Rp ' . number_format($product->promo_price, 0, ',', '.')  }} <span class="ml-1 badge badge-info">promo</span></span>
										@endif
									</td>
								</tr>
								<tr>
									<td>
										<span style="color: #000;">Stok</span>
									</td>
									<td><span style="color: #000;">:</span></td>
									<td>
										@if($product->stock > 0)
											<span style="color: green" class="font-weight-bold">Stok Tersedia</span> <span style="color: #000;">{{ '(' . $product->stock . ' item)' }}</span>
										@elseif($product->stock > 0 && $product->stock < 50)
											<span style="color: yellow" class="font-weight-bold">Stok Mau Habis</span>
										@else
											<span style="color: red" class="font-weight-bold">Habis</span>
										@endif
									</td>
								</tr>
								@if($product->storage_instructions !== null)
									<tr>
										<td>
											<span style="color: #000;">Petunjuk Penyimpanan</span>
										</td>
										<td><span style="color: #000;">:</span></td>
										<td>
											<span style="color: #000;">{{ $product->storage_instructions }}</span>
										</td>
									</tr>
								@endif
								@if($product->storage_period !== null)
									<tr>
										<td>
											<span style="color: #000;">Masa Penyimpanan</span>
										</td>
										<td><span style="color: #000;">:</span></td>
										<td>
											<span style="color: #000;">{{ $product->storage_period }}</span>
										</td>
									</tr>
								@endif
								<tr>
									<td>
										<span style="color: #000;">Unit</span>
									</td>
									<td><span style="color: #000;">:</span></td>
									<td>
                                        <span style="color: #000;">{{ $product->units ?? '-' }}</span>
									</td>
								</tr>
								@if($product->packaging !== null)
									<tr>
										<td>
											<span style="color: #000;">Kemasan</span>
										</td>
										<td><span style="color: #000;">:</span></td>
										<td>
											<span style="color: #000;">{{ $product->packaging }}</span>
										</td>
									</tr>
								@endif
								@if($product->serving_suggestions !== null)
									<tr>
										<td>
											<span style="color: #000;">Saran Penyajian</span>
										</td>
										<td><span style="color: #000;">:</span></td>
										<td>
											<span style="color: #000;">{{ $product->serving_suggestions }}</span>
										</td>
									</tr>
								@endif
								@if($product->start_date !== null && $product->end_date !== null)
									@php
										
										$start = \Carbon\Carbon::parse($product->start_date)->locale('id')->translatedFormat('l, d F Y');
										$end = \Carbon\Carbon::parse($product->end_date)->locale('id')->translatedFormat('l, d F Y');
										$estimation = $start . ' - ' . $end;

									@endphp
									<tr>
										<td>
											<span style="color: #000;">Estimasi Waktu Promo</span>
										</td>
										<td><span style="color: #000;">:</span></td>
										<td>
											<span style="color: #000;">{{ $estimation }}</span>
										</td>
									</tr>
								@endif
								<tr>
									<td>
										<span style="color: #000;">Kategori</span>
									</td>
									<td><span style="color: #000;">:</span></td>
									<td>
										<a href="{{ url('/category/' . $product->category->slug) }}">
											<span>{{ $product->category->name }}</span>
										</a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane fade" id="rating" role="tabpanel" aria-labelledby="rating-tab" style="color: black">
					<p style="font-size: 16px;">
						<i class="fa fa-star mr-1" aria-hidden="true"></i>
						<span class="font-weight-bold">{{ round($product->ratings->avg('rating')) ?? 0 }} / 5</span>
						<span class="font-weight-bold ml-1">({{ $product->ratings->count('comment') ?? 0 }} ulasan)</span>
					</p>
					@forelse($product->ratings as $row)
						@php
							$name = $row->customer->name;
							$maskedName = substr($name, 0, 1) . '****' . substr($name, -1);
						@endphp
						<div class="d-flex flex-column">
							<div class="d-flex align-items-center mb-2">
								<div class="rating">
									@for ($x = 1; $x <= 5; $x++)
										<span 
											class="star {{ $x <= $row->rating ? 'filled' : '' }}" 
											data-value="{{ $x }}"
											title="{{ $x }} Bintang"
										>
											&#9733;
										</span>
									@endfor
								</div>
								<span class="mt-1 ml-2">{{ \Carbon\Carbon::parse($row->created_at)->locale('id')->diffForHumans() }}</span>
							</div>
							<div class="d-flex mb-2">
								<div class="d-flex align-items-center">
									<div style="display: block; height: 30px; width: 30px;">
										<img src="{{ asset('ecommerce/img/blog/author.png') }}" alt="logo" style="width: 100%; height: 100%; object-fit: contain;">
									</div>
									<div class="ml-2 mt-1">
										<span class="font-weight-bold">{{ $maskedName }}</span>
									</div>
								</div>
							</div>
							<div class="d-flex flex-column">
								<p>{{ $row->comment ?? '-' }}</p>
							</div>
						</div>
					@empty
						<p>Belum ada komentar</p>
					@endforelse
				</div>
			</div>
		</div>
	</section>
	<!--================End Product Description Area =================-->
@endsection

@section('js')
    <script>
        $(document).ready(function(){

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
				setTimeout(function() {
					window.location.href = "{{ route('customer.login') }}";
				}, 1500);
            }

			// set timeout for session
            setTimeout(function() {
                $('.alert-success').remove();
                $('.alert-danger').remove();
            }, 2000);
			
			function formatPrice(price) {
				return new Intl.NumberFormat('id-ID', {
					style: 'currency',
					currency: 'IDR',
					minimumFractionDigits: 0
				}).format(price);
			}

			window.increaseQty = function() {
				var result = document.getElementById('sst');
				var sst = result.value;
				console.log('Ditambahin : ', sst);
				if (!isNaN(sst)) {
					result.value++;
					updatePrice();
				}
			};

			window.decreaseQty = function() {
				var result = document.getElementById('sst');
				var sst = result.value;
				console.log('Dikurangin : ', sst);
				if (!isNaN(sst) && sst > 0) {
					result.value--;
					updatePrice();
				}
			};

			$("#sst").on('input', function() {
				var input = $(this).val();
				var validInput = input.replace(/[^0-9]/g, '');  // Allow only numbers
				$(this).val(validInput);

				if (validInput !== '') {
					updatePrice();
				} else {
					$.toast({
						heading: 'Gagal',
						text: 'Kuantiti tidak boleh kosong',
						showHideTransition: 'slide',
						icon: 'error',
						position: 'top-right',
						hideAfter: 2000
					});
					setTimeout(function() {
						window.location.reload(true);
					}, 1000)
				}
			});

			function updatePrice() {
				var qty = $("#sst").val();
				var productPrice = '{{ $product->promo_price ?? $product->price }}';
				if(qty !== 0 && qty !== ''){
					var totalPrice = productPrice * qty;
					$(".harga").text(formatPrice(totalPrice));
				} else {
					$(".harga").text('Rp ' + 0);
				}
			}

			// Add to wishlist
			$('.add-wishlist').click(function(e) {
				e.preventDefault();
				var productId = $(this).data('product-id');
				var form = $('#add-wishlist-form-' + productId);

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				$.ajax({
					url: form.attr('action'),
					method: 'POST',
					data: form.serialize(),
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
                                window.location.reload();
                            }, 1000);
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
					error: function(response) {
						var response = JSON.parse(xhr.responseText);
						if (response.error) {
							errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
						}
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
			});

			// Delete from wishlist
			$('.delete-wishlist').click(function(e) {
				e.preventDefault();
				var wishlistId = $(this).data('wishlist-id');
				var form = $('#delete-wishlist-form-' + wishlistId);

				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				bootbox.confirm({
					message: '<i class="fas fa-exclamation-triangle text-warning mr-2"></i> Kamu yakin menghapus produk ini dari daftar keinginan?',
					backdrop: true,
					buttons: {
						confirm: {
							label: 'Ya <i class="fas fa-check ml-1"></i>',
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
								url: form.attr('action'),
								method: 'DELETE',
								data: form.serialize(),
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
								success: function(response) {
									$.unblockUI();
									if(response.success) {
										$.toast({
											heading: 'Berhasil',
											text: response.success,
											showHideTransition: 'slide',
											icon: 'success',
											position: 'top-right',
											hideAfter: 3000
										});
										setTimeout(function() {
											$('#wishlist-item-' + wishlistId).remove();
											window.location.reload(true);
										}, 1000);
									} else {
										$.toast({
											heading: 'Gagal',
											text: response.error,
											showHideTransition: 'fade',
											icon: 'error',
											position: 'top-right'
										});
									}
								},
								error: function(xhr, status, error) {
									$.unblockUI();
									var response = JSON.parse(xhr.responseText);
									if (response.error) {
										errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
									}
									$.toast({
										heading: 'Gagal',
										text: errorMessage,
										showHideTransition: 'fade',
										icon: 'error',
										position: 'top-right'
									});
								}
							});
						}
					}
				});
			});

			// add to cart
			$('#addToCart').click(function(e){
				e.preventDefault();
				var formData = $('#add-to-cart-form').serialize();

				$.ajax({
					url: "{{ route('front.cart') }}",
					method: "POST",
					data: formData,
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
						if (response.success) {
							$.toast({
								heading: 'Berhasil',
								text: response.success,
								showHideTransition: 'slide',
								icon: 'success',
								position: 'top-right',
								hideAfter: 1000,
							});
							setTimeout(function() {
                                window.location.reload();
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
			});
		});
    </script>
@endsection

@section('css')
	<style>
		.main-button {
			display: inline-block;
			background: #1641ff;
			height: 50px;
			color: #fff;
			font-family: "Roboto", sans-serif;
			font-size: 14px;
			font-weight: 500;
			/* line-height: 48px; */
			border: 1px solid #1641ff;
			border-radius: 0px;
			outline: none !important;
			box-shadow: none !important;
			text-align: center;
			border: 1px solid #1641ff;
			cursor: pointer;
			transition: all 300ms linear 0s;
			border-radius: 5px; 
		}

		.main-button:hover {
			background: transparent;
			color: #1641ff; 
		}

		.grey-button {
			/* line-height: 50px; */
			background: #f9f9ff;
			border: 1px solid #eeeeee;
			border-radius: 5px;
			height: 50px;
			display: inline-block;
			color: #222222;
			font-weight: 500;
		}

		.rating {
			display: inline-flex;
		}

		.star {
			font-size: 26px;
			color: #ccc; /* Default color for unselected stars */
		}

		.star.filled {
			color: gold; /* Color for selected stars */
		}

		.star:hover,
		.star:hover ~ .star {
			color: gold;
		}
	</style>
@endsection