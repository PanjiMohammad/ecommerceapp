@extends('layouts.auth')

@section('title')
<title>Reset Password</title>
@endsection

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <p>Lupa Password</p>
        </div>
        
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="mb-3">
                    <a href="{{ route('login') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                </div>

                <form id="reset-form" action="{{ route('sendPasswordResetLink') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" type="email" name="email" placeholder="Email" value="{{ old('email') }}" autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fas fa-at"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="float-right">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function () {

            // Handle form submission via Ajax
            function updateFormForEmail() {
                var email = $('input[name="email"]').val(); // Get the email value
                
                // Check if the email is 'panji@admin.com'
                if (email === 'panji@admin.com') {
                    // Change the button text
                    $('button[type="submit"]').text('Reset Password User');
                    
                    // Change the form action URL
                    $('#reset-form').attr('action', '{{ route("resetPasswordUser") }}');
                } else {
                    // Reset to default if the email is not 'panji@admin.com'
                    $('button[type="submit"]').text('Reset');
                    $('#reset-form').attr('action', '{{ route("sendPasswordResetLink") }}');
                }
            }

            // Trigger the update function when the email input changes
            $('input[name="email"]').on('keyup change', function () {
                console.log($(this).val());
                updateFormForEmail();
            });

            // Handle form submission via Ajax
            $('#reset-form').on('submit', function (e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
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
                    success: function (response) {
                        $.unblockUI();
                        if (response.success) {
                            Swal.fire({
                                title: 'Sukses',
                                text: response.message,
                                icon: 'success',
                                timer: 2000, // Display for 2 seconds
                                showCancelButton: false,
                                showConfirmButton: false,
                                willClose: () => {
                                    window.location.href = "{{ route('login') }}";
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error',
                                timer: 1500, // Display for 2 seconds
                                showCancelButton: false,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function (xhr) {
                        $.unblockUI();
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';
                            $.each(errors, function (key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                            Swal.fire({
                                title: 'Error',
                                html: errorMessage,
                                icon: 'error',
                                timer: 2000, // Display for 2 seconds
                                showCancelButton: false,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Terjadi kesalahan. Silakan coba lagi nanti.',
                                icon: 'error',
                                timer: 2000, // Display for 2 seconds
                                showCancelButton: false,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            });

        });
    </script>
@endsection