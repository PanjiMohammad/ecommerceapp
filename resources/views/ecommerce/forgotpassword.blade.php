@extends('layouts.ecommerce')

@section('title')
    <title>Lupa Password</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Lupa Password</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Beranda</a>
                        <a href="{{ route('customer.login') }}">Lupa Password</a>
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
				<div class="offset-md-3 col-lg-6">
                    @if (session('success'))
						<input type="hidden" id="success-message" value="{{ session('success') }}">
					@endif
		
					@if (session('error'))
						<input type="hidden" id="error-message" value="{{ session('error') }}">
					@endif

					<div class="login_form_inner">
						<h3>Lupa Password</h3>
						<form id="resetPasswordForm" class="login_form" action="{{ route('customer.resetPassword') }}" method="post" id="contactForm" novalidate="novalidate">
                            @csrf
							<div class="form-group">
								<input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email" required>
								<span class="text-danger">{{ $errors->first('email') }}</span>
							</div>
							<!-- <div class="col-md-12 form-group">
								<div class="creat_account">
									<input type="checkbox" id="f-option2" name="remember">
									<label for="f-option2">Keep me logged in</label>
								</div>
							</div> -->
							<div class="form-group">
								<button type="submit" value="submit" class="btn submit_btn">Reset</button>
								<a href="{{ route('customer.login') }}">Kembali</a>
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
		$(document).ready(function (){

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
			$('#resetPasswordForm').submit(function(e) {
				e.preventDefault();

				var formData = $(this).serialize();

				$.ajax({
					url: "{{ route('customer.resetPassword') }}",
					method: "POST",
					data: formData,
					beforeSend: function() {
                        $('.login_form_inner').block({ 
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
                        $('.login_form_inner').unblock();
                    },
					success: function(response) {
						if(response.success) {
							$.toast({
								heading: 'Berhasil',
								text: response.success,
								showHideTransition: 'slide',
								icon: 'success',
								position: 'top-right',
								hideAfter: 1000
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

		});
	</script>
@endsection