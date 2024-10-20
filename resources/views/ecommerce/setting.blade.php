@extends('layouts.ecommerce')

@section('title')
    <title>Pengaturan - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Pengaturan</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <a href="{{ route('customer.settingForm') }}">Pengaturan</a>
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
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mt-2">Informasi Pribadi</h4>
                                </div>
                                <form id="settingForm" method="post">
                                    @csrf
                                    <div class="card-body loader-area">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" name="email" id="email" class="form-control" required value="{{ $customer->email }}" readonly>
                                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="name">Nama Lengkap</label>
                                                    <input type="text" name="name" id="name" class="form-control" required value="{{ $customer->name }}">
                                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input type="password" name="password" id="password" class="form-control" placeholder="******">
                                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                                    <p class="text-danger">* Biarkan kosong jika tidak ingin mengganti password</p>
                                                </div>
                                                <div class="form-group">
                                                    <label for="phone_number">Nomor Telpon</label>
                                                    <input type="text" name="phone_number" id="phone_number" class="form-control" required value="{{ $customer->phone_number }}">
                                                    <span class="text-danger">{{ $errors->first('phone_number') }}</span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="gender">Jenis Kelamin</label>
                                                    <select name="gender" id="gender" class="form-control" required>
                                                        <option value="pria" {{ $customer->gender == 'pria' ? 'selected' : '' }}>Pria</option>
                                                        <option value="wanita" {{ $customer->gender == 'wanita' ? 'selected' : '' }}>Wanita</option>
                                                    </select>
                                                    <span class="text-danger">{{ $errors->first('gender') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="address">Alamat</label>
                                                    <input type="text" name="address" id="address" class="form-control" required value="{{ $customer->address }}">
                                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                                </div>
                                                <div class="form-group">
                                                    <label for="province_id">Provinsi</label>
                                                    <select class="form-control" name="province_id" id="province_id" required>
                                                        <option value="">Pilih Propinsi</option>
                                                        @foreach ($provinces as $row)
                                                        <option value="{{ $row->id }}" {{ $customer->district->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
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
                                                <div class="form-group" style="margin-top: 10.5%;">
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
                                        <!-- <button class="btn btn-primary btn-sm">Simpan</button> -->
                                        <div class="form-group float-right">
                                            <button class="btn btn-primary btn-md">Simpan</button>
                                        </div>
                                    </div>
                                </form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection


@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <script>

        //JADI KETIKA HALAMAN DI-LOAD
        $(document).ready(function(){
            //MAKA KITA MEMANGGIL FUNGSI LOADCITY() DAN LOADDISTRICT()
            //AGAR SECARA OTOMATIS MENGISI SELECT BOX YANG TERSEDIA
            loadCity($('#province_id').val(), 'bySelect').then(() => {
                loadDistrict($('#city_id').val(), 'bySelect');
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
                                let city_selected = {{ $customer->district->city_id }};
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
                            let district_selected = {{ $customer->district->id }};
                            let selected = type == 'bySelect' && district_selected == item.id ? 'selected':'';
                            $('#district_id').append('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>')
                        })
                    }
                });
            }

            // Handle form submission with AJAX
            $('#settingForm').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengubah profil ini?',
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
                            url: "{{ route('customer.setting') }}",
                            method: "POST",
                            data: $(this).serialize(),
                            beforeSend: function() {
                                $('.loader-area').block({ 
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
                            success: function(response) {
                                $('.loader-area').unblock();
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
                                $('.loader-area').unblock();
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
                                    hideAfter: 5000
                                });
                            }
                        });
                    }
                });
            });
        });

    </script>
@endsection