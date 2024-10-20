@extends('layouts.seller')

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
                            <li class="breadcrumb-item">
                                <a href="{{ route('seller.dashboard') }}">Dashboard</a>
                            </li>
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
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title float-right">ID User : #<span class="font-weight-bold">{{ $seller->id }}</span> | <span class="font-weight-bold">{{ $seller->name }}</span</h4>
                    </div>
                    <form id="settingForm" method="post">
                        @csrf
                        @method('PUT')
                        <div class="card-body settingForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" class="form-control" value="{{ $seller->email }}" readonly>
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Nama</label>
                                        <input type="text" name="name" class="form-control" value="{{ $seller->name }}" required>
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="*****">
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                        <p class="text-danger">* Biarkan kosong jika tidak ingin mengganti password</p>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone_number">Nomor Telpon</label>
                                        <input type="text" name="phone_number" class="form-control" required value="{{ $seller->phone_number }}">
                                        <span class="text-danger">{{ $errors->first('phone_number') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender" class="form-label">Status</label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="pria" {{ $seller->gender == 'pria' ? 'selected':'' }}>Pria</option>
                                            <option value="wanita" {{ $seller->gender == 'wanita' ? 'selected':'' }}>Wanita</option>
                                        </select>
                                        <span class="text-danger">{{ $errors->first('gender') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Alamat</label>
                                        <input type="text" name="address" class="form-control" required value="{{ $seller->address }}">
                                        <span class="text-danger">{{ $errors->first('address') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="province_id">Provinsi</label>
                                        <select class="form-control" name="province_id" id="province_id" required>
                                            <option value="">Pilih Propinsi</option>
                                            @foreach ($provinces as $row)
                                            <option value="{{ $row->id }}" {{ $seller->district->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="city_id">Kabupaten / Kota</label>
                                        <select class="form-control" name="city_id" id="city_id" required>
                                            <option value="">Pilih Kabupaten/Kota</option>
                                        </select>
                                        <span class="text-danger">{{ $errors->first('city_id') }}</span>
                                    </div>
                                    <div class="form-group" style="margin-top: 7.5%;">
                                        <label for="district_id">Kecamatan</label>
                                        <select class="form-control" name="district_id" id="district_id" required>
                                            <option value="">Pilih Kecamatan</option>
                                        </select>
                                        <span class="text-danger">{{ $errors->first('district_id') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group">
                                <button class="btn btn-primary btn-md float-sm-right">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('js')
    <script>

        //JADI KETIKA HALAMAN DI-LOAD
        $(document).ready(function(){
            //MAKA KITA MEMANGGIL FUNGSI LOADCITY() DAN LOADDISTRICT()
            //AGAR SECARA OTOMATIS MENGISI SELECT BOX YANG TERSEDIA
            loadCity($('#province_id').val(), 'bySelect').then(() => {
                loadDistrict($('#city_id').val(), 'bySelect');
            });

            $('#province_id').on('change', function() {
                loadCity($(this).val(), '');
            })

            $('#city_id').on('change', function() {
                loadDistrict($(this).val(), '')
            })

            function loadCity(province_id, type) {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: "{{ url('/api/city') }}",
                        type: "GET",
                        data: { province_id: province_id },
                        success: function(html){
                            $('#city_id').empty()
                            $('#city_id').append('<option value="">Pilih Kabupaten/Kota</option>')
                            $.each(html.data, function(key, item) {
                                
                                // KITA TAMPUNG VALUE CITY_ID SAAT INI
                                let city_selected = {{ $seller->district->city_id }};
                            //KEMUDIAN DICEK, JIKA CITY_SELECTED SAMA DENGAN ID CITY YANG DOLOOPING MAKA 'SELECTED' AKAN DIAPPEND KE TAG OPTION
                                let selected = type == 'bySelect' && city_selected == item.id ? 'selected':'';
                                //KEMUDIAN KITA MASUKKAN VALUE SELECTED DI ATAS KE DALAM TAG OPTION
                                $('#city_id').append('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>')
                                resolve()
                            })
                        }
                    });
                })
            }

            //CARA KERJANYA SAMA SAJA DENGAN FUNGSI DI ATAS
            function loadDistrict(city_id, type) {
                $.ajax({
                    url: "{{ url('/api/district') }}",
                    type: "GET",
                    data: { city_id: city_id },
                    success: function(html){
                        $('#district_id').empty()
                        $('#district_id').append('<option value="">Pilih Kecamatan</option>')
                        $.each(html.data, function(key, item) {
                            let district_selected = {{ $seller->district->id }};
                            let selected = type == 'bySelect' && district_selected == item.id ? 'selected':'';
                            $('#district_id').append('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>')
                        })
                    }
                });
            }

            $('#settingForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                
                $.ajax({
                    url: "{{ route('seller.postSetting', $seller->id) }}",
                    method: "POST",
                    data: formData,
                    beforeSend: function (){
                        $('.settingForm').block({
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
                        $('.settingForm').unblock();
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                willClose: () => {
                                    window.location.href = "{{ route('seller.dashboard') }}";
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'Gagal Memperbarui Data',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = xhr.status + ': ' + xhr.statusText;
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi Kesalahan ' + errorMessage,
                            icon: 'error'
                        });
                    }
                });
            });
        })        

    </script>
@endsection