@extends('layouts.admin')

@section('title')
    <title>Edit Penjual</title>
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
                  <li class="breadcrumb-item"><a href="{{ route('seller.newIndex') }}">Penjual</a></li>
                  <li class="breadcrumb-item active">Edit Penjual</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <!-- TAMBAHKAN ENCTYPE="" KETIKA MENGIRIMKAN FILE PADA FORM -->
            <form id="editSellerForm" action="{{ route('seller.update', $seller->id) }}" method="post" enctype="multipart/form-data" >
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('seller.newIndex') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nama</label>
                                            <input type="text" name="name" class="form-control" value="{{ $seller->name }}">
                                            <span class="text-danger">{{ $errors->first('name') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="text" name="email" class="form-control" value="{{ $seller->email }}" readonly>
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="form-label">Kata Sandi</label>
                                            <input type="password" name="password" class="form-control" placeholder="******">
                                            <p class="text-danger">*Biarkan kosong jika tidak ingin mengganti</p>
                                            <span class="text-danger">{{ $errors->first('password') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone_number" class="form-label">Nomor Telepon</label>
                                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $seller->phone_number }}">
                                            <span class="text-danger">{{ $errors->first('phone_number') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="gender" class="form-label">Status</label>
                                            <select name="gender" id="gender" class="form-control" required>
                                                <option value="pria" {{ $seller->gender == 'pria' ? 'selected':'' }}>Pria</option>
                                                <option value="wanita" {{ $seller->gender == 'wanita' ? 'selected':'' }}>Wanita</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Alamat</label>
                                            <input type="text" class="form-control" id="address" name="address" value="{{ $seller->address }}">
                                            <span class="text-danger">{{ $errors->first('address') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="status" class="form-label">Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="1" {{ $seller->status == '1' ? 'selected':'' }}>Aktif</option>
                                                <option value="0" {{ $seller->status == '0' ? 'selected':'' }}>Tidak Aktif</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="province_id" class="form-label">Provinsi</label>
                                            <select class="form-control" name="province_id" id="province_id" required>
                                                <option value="">Pilih Propinsi</option>
                                                @foreach ($provinces as $row)
                                                <option value="{{ $row->id }}" {{ $seller->district->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                        </div>
                                        <div class="form-group" style="margin-top: 8%;">
                                            <label for="city_id" class="form-label">Kabupaten / Kota</label>
                                            <select class="form-control" name="city_id" id="city_id" required>
                                                <option value="">Pilih Kabupaten/Kota</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('city_id') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="district_id" class="form-label">Kecamatan</label>
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
                                    <button type="submit" class="btn btn-primary float-right">Ubah</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
            })

            // Submit form via AJAX
            $('#editSellerForm').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                var form = $(this);
                var url = form.attr('action');
                var method = form.attr('method');
                var formData = form.serialize(); // Serialize form data

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        $.blockUI({ 
                            message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
                            css: { 
                                border: 'none', 
                                padding: '15px', 
                                backgroundColor: '#000', 
                                '-webkit-border-radius': '10px', 
                                '-moz-border-radius': '10px', 
                                opacity: .5, 
                                color: '#fff' 
                            } 
                        });
                    },
                    success: function(response) {
                        $.unblockUI();
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success'
                            }).then(function() {
                                window.location.href = "{{ route('seller.newIndex') }}";
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi Kesalahan ' + xhr.responseText,
                            icon: 'error'
                        });
                    }
                });
            });
        })

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
    </script>
@endsection