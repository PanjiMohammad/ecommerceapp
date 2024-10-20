@extends('layouts.auth')

@section('title')
<title>Login</title>
@endsection

@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href=#><b>E-</b>COMMERCE</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <form id="loginForm" action="{{ route('post.newLogin') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" type="email" name="email" placeholder="Email" value="{{ old('email') }}" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <span class="text-danger" id="email_error"></span>
                <div class="input-group mb-3">
                    <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" type="password" name="password" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <span class="text-danger" id="password_error"></span>
                <div class="row">
                    <div class="col-8">
                        <p class="mt-2">
                            <a href="{{ route('forgotPassword') }}">Lupa Password</a>
                        </p>
                    </div>

                    <div class="col-4">
                        <div class="mt-1">
                            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                        </div>
                    </div>
                </div>

                <div class="mt-2 text-center">
                    <span>Tidak punya akun? <a href="{{ route('register') }}">Daftar</a></span><br>
                </div>
            </form>
        </div>
    <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->
@endsection

@section('js')

    <!-- set session with toast -->

    <script>
        $(document).ready(function() {

            // set timeout for session
            setTimeout(function() {
                $('.alert-success').remove();
                $('.alert-danger').remove();
            }, 2000);

            // login form
			$('#loginForm').submit(function(e) {
				e.preventDefault();

				var formData = $(this).serialize();

				$.ajax({
					url: "{{ route('post.newLogin') }}",
					method: "POST",
					data: formData,
					beforeSend: function() {
                        $('.login-card-body').block({ 
                            message: '<i class="fa fa-spinner"></i>',
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
                        $('.login-card-body').unblock();
                    },
					success: function(response) {
						Swal.fire({
                            title: 'Berhasil',
                            text: response.success,
                            icon: 'success',
                            timer: 1500, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = response.redirect;
                            }
                        });
					},
					error: function(xhr, status, error) {
                        var test = xhr.status == 500 ? true : false;
                        let errors = xhr.responseJSON.errors;
                        let input = xhr.responseJSON.input;

                        // Clear previous errors
                        $('.text-danger').text('');

						var response = JSON.parse(xhr.responseText);
						if (response.error) {
							errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
						}
                        Swal.fire({
                            title: 'Gagal',
                            text: errorMessage,
                            icon: 'error',
                            timer: 2000, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                if(xhr.status == 400) {
                                    // Display validation errors using SweetAlert
                                    let errorMessage = '';
                                    $.each(errors, function(key, error) {
                                        errorMessage += error[0] + '<br>';
                                        $('#' + key + '_error').text(error[0]);

                                        $('#' + key).addClass('input-error');

                                        // Set timeout to clear the error text after 3 seconds
                                        setTimeout(function() {
                                            $('#' + key + '_error').text('');
                                            $('#' + key).removeClass('input-error');
                                        }, 3000);
                                    });

                                    // Retain input values
                                    $.each(input, function(key, value) {
                                        $('#' + key).val(value);
                                    });
                                } else {
                                    window.location.reload(true);
                                }
                            }
                        });
					}
				});
			});

        });
    </script>
@endsection

@section('css')
    <style>
        .input-error {
            border: 1px solid red;
        }
    </style>
@endsection