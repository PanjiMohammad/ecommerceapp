@extends('layouts.ecommerce')

@section('title')
    <title>Dashboard - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Dashboard</h2>
					<div class="page_link">
                        <!-- <a href="{{ url('/') }}">Home</a> -->
                        <a href="{{ route('customer.dashboard') }}">Dashboard</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
	<section class="login_box_area p_120">
		<div class="container">
			@if (session('success'))
				<input type="hidden" id="success-message" value="{{ session('success') }}">
			@endif

			@if (session('error'))
				<input type="hidden" id="error-message" value="{{ session('error') }}">
			@endif
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    <div class="slider">
						<div class="col-md-4">
							<div class="card text-center" style="border-radius: 7px; background: linear-gradient(135deg, #FFDEE9 0%, #B5FFFC 100%);">
								<div class="card-body">
									<h3 class="text-dark">Belum Dibayar</h3>
									<hr class="text-dark">
									<p class="test text-dark">Rp {{ number_format($orders[0]->pending, 0, ',', '.') }}</p>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card text-center" style="border-radius: 7px; background: linear-gradient(135deg, #FBD786 0%, #f7797d 100%);">
								<div class="card-body">
									<h3 class="text-dark">Dikonfirmasi</h3>
									<hr class="text-dark">
									<p class="text-dark">{{ $orders[0]->confirm }} Pesanan</p>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card text-center" style="border-radius: 7px; background: linear-gradient(135deg, #FF9A9E 0%, #fecfef 100%);">
								<div class="card-body">
									<h3 class="text-dark">Proses</h3>
									<hr class="text-dark">
									<p class="text-dark">{{ $orders[0]->process }} Pesanan</p>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card text-center" style="border-radius: 7px; background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);">
								<div class="card-body">
									<h3 class="text-dark">Dikirim</h3>
									<hr class="text-dark">
									<p class="test text-dark">{{ $orders[0]->shipping . ' Pesanan' }}</p>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card text-center" style="border-radius: 7px; background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);">
								<div class="card-body">
									<h3 class="text-dark">Sampai</h3>
									<hr class="text-dark">
									<p class="text-dark">{{ $orders[0]->arrive }} Pesanan</p>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card text-center" style="border-radius: 7px; background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">
								<div class="card-body">
									<h3 class="text-dark">Selesai</h3>
									<hr class="text-dark">
									<p class="text-dark">{{ $orders[0]->completeOrder }} Pesanan</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('js')
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
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
            }

			// slider
			$('.slider').slick({
				slidesToShow: 3, 
				slidesToScroll: 1, 
				infinite: false, 
				dots: true, 
				prevArrow: '<button type="button" class="slick-prev custom-arrow"><i class="fa fa-chevron-left"></i></button>',
    			nextArrow: '<button type="button" class="slick-next custom-arrow"><i class="fa fa-chevron-right"></i></button>',
				responsive: [
					{
						breakpoint: 768,
						settings: {
							slidesToShow: 2,
							slidesToScroll: 1,
							infinite: true,
							dots: true
						}
					},
					{
						breakpoint: 576,
						settings: {
							slidesToShow: 1,
							slidesToScroll: 1,
							infinite: true,
							dots: true
						}
					}
				]
			});
		});
	</script>
@endsection

@section('css')
	<style>
		.custom-arrow {
			font-size: 18px;
			color: #333; /* Arrow color */
			z-index: 1;
			top: 50%;
			transform: translateY(-50%);
			background: none;
			border: none;
			cursor: pointer;
			transition: color 0.3s ease, transform 0.3s ease; /* Smooth transition for color change */
		}

		.slick-prev.custom-arrow {
			left: -15px; /* Position left arrow */
		}

		.slick-next.custom-arrow {
			right: -15px; /* Position right arrow */
		}

		/* Hover effect */
		.custom-arrow:hover {
			color: #ff5722; /* Change arrow color on hover */
			transform: translateY(-50%) scale(1.2); /* Slightly enlarge arrow on hover */
		}
	</style>
@endsection