@extends('layouts.ecommerce')

@section('title')
    <title>Login - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Login</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <a href="{{ route('customer.login') }}">Login</a>
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
				<div class="offset-md-3 col-lg-6">
					@if (session('success'))
						<input type="hidden" id="success-message" value="{{ session('success') }}">
					@endif
		
					@if (session('error'))
						<input type="hidden" id="error-message" value="{{ session('error') }}">
					@endif

					<div class="login_form_inner">
						<h3>Login</h3>
						<form id="loginForm" class="login_form" action="{{ route('customer.post_login') }}" method="post" id="contactForm" novalidate="novalidate">
                            @csrf
							<div class="form-group">
								<input type="email" class="form-control" id="email" name="email" placeholder="Email">
							</div>
							<div class="form-group">
								<input type="password" class="form-control" id="password" name="password" placeholder="******">
							</div>
							<!-- <div class="form-group">
								<div class="creat_account">
									<input type="checkbox" id="f-option2" name="remember">
									<label for="f-option2">Keep me logged in</label>
								</div>
							</div> -->
							<div class="forgot-password float-right mt-2 mb-3">
								<a style="color: #777;" href="{{ route('customer.forgotPassword') }}">Lupa Kata Sandi</a>
							</div>
							<div class="form-group">
								<button type="submit" value="submit" class="btn submit_btn">Log In</button>
							</div>
							<p class="mt-5">Atau login menggunakan : </p>
							<div class="another-buttons text-center mt-1">
								<a href="#" class="btn btn-google"><i class="fab fa-google"></i></a>
								<a href="#" class="btn btn-facebook"><i class="fab fa-facebook"></i></a>
								<a href="#" class="btn btn-twitter"><i class="fab fa-twitter"></i></a>
							</div>
							<div class="member-sign-up" style="margin-top: 10rem;">
								<span>Ingin bergabung sebagai member?</span><br>
								<a href="{{ route('customer.register') }}">Daftar Disini</a>
							</div>
						</form>
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

			// login form
			$('#loginForm').submit(function(e) {
				e.preventDefault();

				var formData = $(this).serialize();

				$.ajax({
					url: "{{ route('customer.post_login') }}",
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
                                window.location.href = "{{ route('customer.dashboard') }}";
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
						setTimeout(function() {
							window.location.reload(true);
						}, 1500);
					}
				});
			});

		})
	</script>
@endsection

@section('css')
	<style>
		.another-buttons {
			display: flex;
			justify-content: center;
			gap: 10px;
			margin-top: 15px;
		}

		.another-buttons a {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 40px;
			height: 40px;
			border-radius: 50%;
			color: #fff;
			font-size: 20px;
			text-decoration: none;
			transition: background-color 0.3s ease;
		}

		.btn-google {
			background-color: #db4437;
		}

		.btn-facebook {
			background-color: #3b5998;
		}

		.btn-twitter {
			background-color: #1da1f2;
		}

		.another-buttons a:hover {
			opacity: 0.8;
		}
	</style>
@endsection