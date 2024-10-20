@extends('layouts.admin')

@section('title')
    <title>Pengaturan Konten</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Pengturan Konten</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengaturan Konten</li>
                    </ol>
                </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="row">
                    <!-- BAGIAN INI AKAN MENG-HANDLE FORM EDIT USER  -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('home') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                            </div>
                            <form id="content-settings-form" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card-body loader-area">
                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif

                                    @if (session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mb-3" style="border: 1px solid transparent; padding: 0 40% 0 0;">
                                        <div class="content-image">
                                            <img id="current-logo" src="{{ asset('storage/' . \App\Helpers\SettingsHelper::get('logo')) }}" alt="Current Logo" style="max-height: 150px; margin-bottom: 10px;">
                                        </div>

                                        <div class="form-group">
                                            <label for="logo">Logo</label>
                                            
                                            <input type="file" name="logo" id="logo" class="form-control">
                                            <span id="" class="text-danger">{{ $errors->first('logo') }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="about">Tentang Kami</label>
                                                <textarea name="about" id="about" rows="4" cols="50" class="form-control" placeholder="Masukkan Deskripsi">{{ $aboutContent }}</textarea>
                                                <span class="text-danger">{{ $errors->first('about') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="hot_deals_visibility">Promo</label>
                                                <select name="hot_deals_visibility" id="hot_deals_visibility" class="form-control">
                                                    <option value="show" {{ \App\Helpers\SettingsHelper::getHotDealsVisibility() == 'show' ? 'selected' : '' }}>Tampil</option>
                                                    <option value="hide" {{ \App\Helpers\SettingsHelper::getHotDealsVisibility() == 'hide' ? 'selected' : '' }}>Sembunyikan</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-md float-right">Ubah</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- BAGIAN INI AKAN MENG-HANDLE FORM EDIT PROFILE USER  -->
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('js')
    <!-- CKEditor 5 CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {

            // Initialize CKEditor 5
            let aboutEditor;

            ClassicEditor
                .create(document.querySelector('#about'))
                .then(editor => {
                    aboutEditor = editor;
                })
                .catch(error => {
                    console.error('There was a problem initializing CKEditor:', error);
                });

            // Handle form submission via Ajax
            $('#content-settings-form').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                formData.set('_method', 'PUT');

                // Get data from CKEditor
                formData.set('about', aboutEditor.getData());

                // Handle Hot Deals visibility
                var hotDealsVisibility = $('#hot_deals_visibility').val();
                formData.set('hot_deals_visibility', hotDealsVisibility);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengubah konten ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('user.postContentSetting') }}',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
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
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000, // Display for 2 seconds
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            location.reload(true);
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
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
                    }
                });
            });

        });
    </script>
@endsection