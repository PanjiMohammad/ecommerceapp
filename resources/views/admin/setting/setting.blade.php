@extends('layouts.admin')

@section('title')
    <title>Pengaturan Akun</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Pengturan Akun</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pengaturan Akun</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="row">
                    <!-- BAGIAN INI AKAN MENG-HANDLE FORM EDIT USER  -->
                    <div class="col-md-12">
                        <form id="settingForm" method="post" action="{{ route('user.postAccountSetting') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="user_id" value="{{ $admin->id }}">
                            <div class="card">
                                <div class="card-header">
                                    <a href="{{ route('home') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                                </div>
                                <div class="card-body loader-area">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" class="form-control" value="{{ $admin->email }}" readonly>
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Nama</label>
                                        <input type="text" name="name" class="form-control" value="{{ $admin->name }}" required>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="*****">
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                        <p class="text-danger">* Biarkan kosong jika tidak ingin mengganti password</p>
                                    </div>          
                                </div>
                                <div class="card-footer">
                                    <div class="form-group float-sm-right">
                                        <button type="submit" class="btn btn-primary btn-md">Ubah</button>
                                    </div>     
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- BAGIAN INI AKAN MENG-HANDLE FORM EDIT PROFILE USER  -->
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            
            $('#settingForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Get the form data
                var formData = $(this).serialize();

                // Send the form data via Ajax
                $.ajax({
                    url: $(this).attr('action'), // Form action URL (route to postAccountSetting)
                    method: 'PUT', // HTTP method
                    data: formData, // Form data
                    beforeSend: function() {
                        $('.loader-area').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
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
                        $('.loader-area').unblock();
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.success,
                                icon: 'success',
                                timer: 2000, // Display for 2 seconds
                                showCancelButton: false,
                                showConfirmButton: false,
                                willClose: () => {
                                    window.location.href = "{{ route('home') }}"
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Gagal',
                                icon: 'error',
                                timer: 2000, // Display for 2 seconds
                                showCancelButton: false,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function(response) {
                        var errors = response.responseJSON.errors;
                        Swal.fire({
                            title: 'Error',
                            text: response.message + errors,
                            icon: 'error',
                            timer: 2000, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.reload(true);
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection