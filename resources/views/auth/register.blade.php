@extends('layouts.auth')

@section('title')
<title>Register</title>
@endsection

@section('content')
    
    <div class="login-box">
        <div class="login-logo">
            <h2>Registrasi</h2>
        </div>
        
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body loader-area">
                @if (session('success'))
                    <input type="hidden" id="success-message" value="{{ session('success') }}">
                @endif

                @if (session('error'))
                    <input type="hidden" id="error-message" value="{{ session('error') }}">
                @endif

                <div class="mb-3">
                    <a href="{{ route('login')}}" style="color: black;"><i class="fa fa-arrow-left" style="color: black;"></i> <span class="ml-1">Kembali</span></a>
                </div>
                <form id="registerForm" action="{{ route('post.newRegister') }}" method="post" novalidate="novalidate">
                    @csrf
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                        <span class="text-danger" id="name_error"></span>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
                        <span class="text-danger" id="email_error"></span>
                    </div>
                    {{-- <div class="form-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Kata Sandi" required>
                        <span class="text-danger" id="password_error"></span>
                    </div> --}}
                    <div class="form-group">
                        <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Masukkan Nomor Telepon" required>
                        <span class="text-danger" id="phone_number_error"></span>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="address" name="address" placeholder="Masukkan Alamat" required>
                        <span class="text-danger" id="address_error"></span>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="province_id" id="province_id" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach ($provinces as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="province_id_error"></span>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="city_id" id="city_id" required>
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                        <span class="text-danger" id="city_id_error"></span>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="district_id" id="district_id" required>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <span class="text-danger" id="district_id_error"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function(){

            var successMessage = $('#success-message').val();
            var errorMessage = $('#error-message').val();

            if (successMessage) {
                Swal.fire({
                    title: 'Berhasil',
                    text: successMessage,
                    icon: 'success'
                });
            }

            if (errorMessage) {
				Swal.fire({
                    title: 'Gagal',
                    text: errorMessage,
                    icon: 'error'
                });
            }

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
                            console.log(item)
                            $('#city_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                        })
                    }
                });
            })

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
            })

            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: "POST",
                    data: formData,
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
                        if (response.success == true) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.success,
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
                                title: 'Gagal',
                                text: response.error,
                                icon: 'error',
                                timer: 1500, // Display for 2 seconds
                                showCancelButton: false,
                                showConfirmButton: false,
                                willClose: () => {
                                    window.location.reload(true);
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        $('.loader-area').unblock();
                        let errors = xhr.responseJSON.error;
                        let input = xhr.responseJSON.input;

                        // Clear previous errors
                        $('.text-danger').text('');

                        // Display validation errors using SweetAlert
                        let errorMessage = '';
                        $.each(errors, function(key, error) {
                            errorMessage += error[0] + '<br>';
                            $('#' + key + '_error').text(error[0]);
                        });

                        // Retain input values
                        $.each(input, function(key, value) {
                            $('#' + key).val(value);
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: 'Terjadi Kesalahan',
                            timer: 2000, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                        });

                    }
                });
            });

        })
    </script>
@endsection