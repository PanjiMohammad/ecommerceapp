@extends('layouts.admin')

@section('title')
    <title>Tambah Penjual</title>
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Penjual</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Penjual</a></li>
                        <li class="breadcrumb-item active">Tambah Penjual</li>
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
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('seller.newIndex') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                        </div>
                        <form id="addSellerForm" action="{{ route('seller.store') }}" method="post">
                            @csrf
                            <div class="card-body loader-area">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nama</label>
                                            <input type="text" name="name" class="form-control" placeholder="Masukan Nama Lengkap">
                                            <span class="text-danger" id="name_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="text" name="email" class="form-control" placeholder="Masukan Email">
                                            <span class="text-danger" id="email_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone_number">Nomor Telpon</label>
                                            <input type="number" name="phone_number" id="phone_number" class="form-control" placeholder="Masukkan Nomor Telpon">
                                            <span class="text-danger" id="phone_number_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="address" class="form-label">Alamat</label>
                                            <input type="text" class="form-control" id="address" name="address" placeholder="Masukkan Alamat">
                                            <span class="text-danger" id="address_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="gender" class="form-label">Jenis Kelamin</label>
                                            <select name="gender" id="gender" class="form-control">
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="pria">Pria</option>
                                                <option value="wanita">Wanita</option>
                                            </select>
                                            <span class="text-danger" id="gender_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1">Aktif</option>
                                                <option value="0">Tidak Aktif</option>
                                            </select>
                                            <span class="text-danger" id="status_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="province_id" class="form-label">Pilih Provinsi</label>
                                            <select class="form-control" name="province_id" id="province_id">
                                                <option value="">Pilih Provinsi</option>
                                                <!-- LOOPING DATA PROVINCE UNTUK DIPILIH OLEH CUSTOMER -->
                                                @foreach ($provinces as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger" id="province_id_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="city_id" class="form-label">Pilih Kabupaten</label>
                                            <select class="form-control" name="city_id" id="city_id">
                                                <option value="">Pilih Kabupaten/Kota</option>
                                            </select>
                                            <span class="text-danger" id="city_id_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="district_id" class="form-label">Pilih Kecamatan</label>
                                            <select class="form-control" name="district_id" id="district_id">
                                                <option value="">Pilih Kecamatan</option>
                                            </select>
                                            <span class="text-danger" id="district_id_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary float-right">Simpan</button>
                                </div>
                            </div>
                        </form>    
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('js')
    <script>
        $(document).ready(function (){

            //KETIKA SELECT BOX DENGAN ID province_id DIPILIH
            $('#province_id').on('change', function() {
                //MAKA AKAN MELAKUKAN REQUEST KE URL /API/CITY DENGAN MENGIRIM PROVINCE_ID
                $.ajax({
                    url: "{{ url('/api/city') }}",
                    type: "GET",
                    data: { province_id: $(this).val() },
                    success: function(html){
                        //SETELAH DATA DITERIMA, SELECTBOX DENGAN ID CITY_ID DI KOSONGKAN
                        $('#city_id').empty()
                        //KEMUDIAN APPEND DATA BARU YANG DIDAPATKAN DARI HASIL REQUEST VIA AJAX
                        //UNTUK MENAMPILKAN DATA KABUPATEN / KOTA
                        $('#city_id').append('<option value="">Pilih Kabupaten/Kota</option>')
                        $.each(html.data, function(key, item) {
                            $('#city_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                        })
                    }
                });
            });

            //LOGICNYA SAMA DENGAN CODE DIATAS HANYA BERBEDA OBJEKNYA SAJA
            $('#city_id').on('change', function() {
                $.ajax({
                    url: "{{ url('/api/district') }}",
                    type: "GET",
                    data: { city_id: $(this).val() },
                    success: function(html){
                        $('#district_id').empty()
                        $('#district_id').append('<option value="">Pilih Kecamatan</option>')
                        $.each(html.data, function(key, item) {
                            $('#district_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                        })
                    }
                });
            });

            $('#addSellerForm').on('submit', function(e){
                e.preventDefault();

                var formData = $(this).serializeArray();
                console.log(formData);
                var actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
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
                        $('.loader-area').unblock(); // Hide loader after request complete
                    },
                    success: function(response){
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.success,
                            icon: 'success',
                            timer: 2000, 
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = "{{ route('seller.newIndex') }}";
                            }
                        });
                    },
                    error: function(xhr, status, error) {
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
                            timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
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
                            }
                        });
                    }
                })
            });

        });
    </script>
@endsection