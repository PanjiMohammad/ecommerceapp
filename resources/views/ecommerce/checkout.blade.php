@extends('layouts.ecommerce')

@section('title')
    <title>Checkout - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
    <section class="banner_area">
        <div class="banner_inner d-flex align-items-center">
            <div class="container">
                <div class="banner_content text-center">
                    <h2>Informasi Pengiriman</h2>
                    <div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <a href="">Pengiriman</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Home Banner Area =================-->

    <!--================Checkout Area =================-->
    <section class="checkout_area section_gap">
        <div class="container">
            <div class="billing_details">
                <form id="checkoutForm" action="{{ route('front.store_checkout') }}" method="post" novalidate="novalidate">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <span class="font-weight-bold text-dark" style="font-size: 16px;">Alamat Pengiriman</span>
                                        <!-- Button to trigger the modal -->
                                        <a href="#" class="btn btn-link btn-sm" data-toggle="modal" data-target="#ubahAlamatModal">
                                            Ubah
                                        </a>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fa-solid fa-location-dot mr-2 text-dark"></i>
                                        <span class="text-dark font-weight-bold">Alamat Rumah • {{ $customer->name }}</span>
                                    </div>
                                    <span class="text-dark">{{ $customer->address . ', Kecamatan ' . $customer->district->name . ', Kota ' . $customer->district->city->name . ', ' . $customer->district->city->province->name . ' ' . $customer->district->city->postal_code }}</span>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    @php $i = 1; @endphp
                                    @foreach($carts as $cart)
                                        <input type="hidden" name="seller_id[]" value="{{ $cart['seller_id'] }}" class="form-control">
                                        <div style="border-bottom: 1px solid #ededed; padding-bottom: 20px; margin-bottom: 15px;">
                                            <i class="fa fa-user mr-1 text-dark font-weight-bold"></i>
                                            <span class="text-dark font-weight-bold">Penjual : {{ $cart['seller_name'] }}</span>
                                            @foreach($cart['products'] as $product)
                                                <input type="hidden" name="qty[]" id="sst{{ $product['product_id'] }}" maxlength="12" value="{{ $product['qty'] }}" title="Quantity:" class="input-text qty">
                                                <input type="hidden" name="product_id[]" value="{{ $product['product_id'] }}" class="form-control">
                                                <input type="hidden" name="weight[]" value="{{ $product['weight'] }}" class="form-control">
                                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div style="width: 80px; height: 80px; display: block; border: 1px solid #ededed; border-radius: 5px;">
                                                            <img src="{{ asset('/products/' . $product['product_image']) }}" alt="{{ $product['product_name'] }}" style="width: 100%; height: 100%; object-fit: contain;">
                                                        </div>
                                                        <div class="d-flex flex-column ml-3">
                                                            <span class="text-dark font-weight-bold">{{ $product['product_name'] }}</span>
                                                            @php
                                                                $weight = $product['weight'];
            
                                                                if (strpos($weight, '-') !== false) {
                                                                    // If the weight is a range, split it into an array
                                                                    $weights = explode('-', $weight);
                                                                    
                                                                    // Use the maximum value in the range for passing to controller
                                                                    $maxWeight = (int) trim(max($weights));
            
                                                                    $minWeight = (float) trim($weights[0]);
                                                                    $maxWeight = (float) trim($weights[1]);
            
                                                                    // Check if the weights are >= 1000 to display in Kg
                                                                    $minWeightDisplay = $minWeight >= 1000 ? ($minWeight / 1000) : $minWeight;
                                                                    $maxWeightDisplay = $maxWeight >= 1000 ? ($maxWeight / 1000) . ' Kg' : $maxWeight . ' gram / pack';
            
                                                                    // Construct the display string
                                                                    $weightDisplay = $minWeightDisplay . ' - ' . $maxWeightDisplay;
                                                                } else {
                                                                    $maxWeight = (int) trim($weight);
            
                                                                    // Single weight value
                                                                    $weightDisplay = $weight >= 1000 ? ($weight / 1000) . ' Kg' : $weight . ' gram / pack';
                                                                }
                                                            @endphp
                                                            <span>{{ $weightDisplay }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span class="text-dark font-weight-bold">{{ $product['qty'] . ' x Rp ' . number_format($product['product_price'], 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="row mt-3">
                                                @if($cart['courier'] != null && $cart['service'] != null && $cart['shippingCost'] != null)
                                                    <input type="hidden" name="ongkir[]" id="ongkir{{ $i }}" value="{{ $cart['shippingCost'] }}" class="form-control">
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <select class="form-control form-control-sm" name="courier[]" id="courier{{ $i }}" onchange="pilihkurir('{{ $i }}', {{ $maxWeight }}, {{ $cart['origin_details']['city_id'] }})">
                                                                <option value="jne" {{ $cart['courier'] == 'jne' ? 'selected' : '' }}>JNE</option>
                                                                <option value="pos" {{ $cart['courier'] == 'pos' ? 'selected' : '' }}>POS</option>
                                                                <option value="tiki" {{ $cart['courier'] == 'tiki' ? 'selected' : '' }}>TIKI</option>
                                                            </select>
                                                            <span class="text-danger">{{ $errors->first('courier') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <div class="form-group loaderAreaService{{ $i }}">
                                                            <select class="form-control form-control-sm" name="service[]" id="service{{ $i }}" onchange="pilihlayanan('{{ $i }}', {{ $maxWeight  }})">
                                                                <option value="{{ $cart['service'] }}">{{ $cart['service'] }}</option>
                                                            </select>
                                                            <span class="text-danger">{{ $errors->first('service') }}</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <input type="hidden" name="ongkir[]" id="ongkir{{ $i }}" value="" class="form-control">
                                                    <div class="col-lg-4">
                                                        <div class="form-group">
                                                            <select class="form-control form-control-sm" name="courier[]" id="courier{{ $i }}" onchange="pilihkurir('{{ $i }}', {{ $maxWeight  }}, {{ $cart['origin_details']['city_id'] }})">
                                                                <option value="">Pilih Kurir</option>
                                                                <option value="jne">JNE</option>
                                                                <option value="pos">POS</option>
                                                                <option value="tiki">TIKI</option>
                                                            </select>
                                                            <span class="text-danger">{{ $errors->first('courier') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <div class="form-group loaderAreaService{{ $i }}">
                                                            <select class="form-control form-control-sm" name="service[]" id="service{{ $i }}" onchange="pilihlayanan('{{ $i }}', {{ $maxWeight  }})">
                                                                <option value="">Pilih Layanan</option>
                                                            </select>
                                                            <span class="text-danger">{{ $errors->first('service') }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @php $i++; @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <span class="style-new" style="font-size: 1.17em;">Ringkasan Belanja</span>
                                    @php
                                        $countCarts = collect($carts)->sum(function ($cart) {
                                            return count($cart['products']);
                                        });

                                        $countSeller = collect($carts)->count();
                                        
                                    @endphp
                                    <div class="mt-3 d-flex justify-content-between align-items-center">
                                        <span>Subtotal ({{ $countCarts }} item)</span>
                                        <span>{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div id="shippingCostDiv" class="d-flex justify-content-between align-items-center mt-2">
                                        <span>Total Ongkos Kirim</span>
                                        <span id="shipping_cost">Rp {{ $shippingCost !== 0 ? number_format($shippingCost, 0, ',', '.') : 0 }}</span>
                                    </div>
                                    <div id="serviceCostDiv" class="d-flex justify-content-between align-items-center mt-2">
                                        <span>Biaya Layanan</span>
                                        <span id="service_cost">Rp {{ number_format($serviceCost, 0, ',', '.') }}</span>
                                    </div>
                                    <div id="packagingDiv" class="d-flex justify-content-between align-items-center mt-2">
                                        <span>Biaya Kemasan {{ '(' . $countSeller . ' penjual)' }}</span>
                                        <span id="packaging_cost">Rp {{ number_format($packagingCost, 0, ',', '.') }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark font-weight-bold">Total Belanja</span>
                                        <span class="text-dark font-weight-bold" id="shippingTotal">Rp {{ number_format($shippingCost + $subtotal + $packagingCost + $serviceCost, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <div class="card-footer" style="background-color: white;">
                                    <button type="submit" class="btn btn-primary btn-success w-100 loaderAreaPurchase">Lanjut Bayar</button>
                                    <div style="padding: 10px; 5px; text-align: center;">
                                        <span style="font-size: 12px; line-height: 1px;">Dengan melanjutkan, kamu menyetujui syarat & ketentuan.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!--================End Checkout Area =================-->

    <!-- Modal Change Address Structure -->
    <div class="modal fade" id="ubahAlamatModal" tabindex="-1" role="dialog" aria-labelledby="ubahAlamatModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 70%; max-height: 100%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ubahAlamatModalLabel">Ubah Alamat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="settingForm" method="post">
                    @csrf
                    <div class="modal-body loaderArea">
                        <div class="row">
                            <div class="col-sm-6 form-group p_star">
                                <label for="email" class="form-label font-weight-bold">Email</label>
                                @if (auth()->guard('customer')->check())
                                    <input type="email" class="form-control" id="email" name="email" 
                                    value="{{ $customer->email }}" 
                                    required {{ auth()->guard('customer')->check() ? 'readonly':'' }}>
                                @else
                                    <input type="email" class="form-control" id="email" name="email" required>
                                @endif
                                <span class="text-danger" id="email_error"></span>
                            </div>
                            <div class="col-sm-6 form-group p_star">
                                <label for="address" class="form-label font-weight-bold">Alamat Lengkap</label>
                                <!-- <input type="text" class="form-control" id="address" name="address" required> -->
                                <textarea class="form-control" name="address" id="address" placeholder="Alamat Lengkap Penerima">{{ $customer->address }}</textarea>
                                <span class="text-danger" id="address_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group p_star">
                                <label for="email" class="form-label font-weight-bold">Nama Penerima</label>
                                <input type="text" class="form-control" id="first" placeholder="Nama Penerima" name="name" value="{{ $customer->name }}" readonly>
                                <span class="text-danger" id="email_error"></span>
                            </div>
                            <div class="col-sm-6 form-group p_star">
                                <label for="province_id" class="form-label font-weight-bold">Provinsi</label>
                                <select class="form-control" name="province_id" id="province_id">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinces as $row)
                                        <option value="{{ $row->id }}" {{ $customer->district->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="province_id_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group p_star">
                                <label for="phone_number" class="form-label font-weight-bold">Nomor Telepon</label>
                                <input type="text" class="form-control" id="phone_number" placeholder="Nomor Telepon Penerima" name="phone_number" value="{{ $customer->phone_number }}" readonly>
                                <span class="text-danger" id="phone_number_error"></span>
                            </div>
                            <div class="col-sm-6 form-group p_star loading-area-city">
                                <label for="email" class="form-label font-weight-bold">Kabupaten / Kota</label>
                                <select class="form-control" name="city_id" id="city_id">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                </select>
                                <span class="text-danger" id="city_id_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group p_star">
                                <label for="gender" class="form-label font-weight-bold">Jenis Kelamin</label>
                                <select class="form-control" name="gender" id="gender" disabled>
                                    <option value="pria" {{ $customer->gender == 'pria' ? 'selected' : '' }}>Pria</option>
                                    <option value="wanita" {{ $customer->gender == 'wanita' ? 'selected' : '' }}>Wanita</option>
                                </select>
                                <input type="hidden" name="gender" id="gender-hidden" value="{{ $customer->gender }}">
                                <span class="text-danger" id="gender_error"></span>
                            </div>
                            <div class="col-sm-6 form-group p_star">
                                <label for="district_id" class="form-label font-weight-bold">Kecamatan</label>
                                <select class="form-control" name="district_id" id="district_id">
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <span class="text-danger" id="district_id_error"></span>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-sm-4 form-group p_star">
                                <label class="form-label font-weight-bold">Provinsi</label>
                                <select class="form-control" name="province_id" id="province_id" required>
                                    <option value="">Pilih Propinsi</option>
                                    @foreach ($provinces as $row)
                                        <option value="{{ $row->id }}" {{ $customer->district->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="province_id_error"></span>
                            </div>
                            <div class="col-sm-4 form-group p_star">
                                <label class="form-label font-weight-bold">Kabupaten / Kota</label>
                                <select class="form-control" name="city_id" id="city_id" required>
                                    <option value="">Pilih Kabupaten/Kota</option>
                                </select>
                                <span class="text-danger" id="city_id_error"></span>
                            </div>
                            <div class="col-sm-4 form-group p_star">
                                <label class="form-label font-weight-bold">Kecamatan</label>
                                <select class="form-control" name="district_id" id="district_id" required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <span class="text-danger" id="district_id_error"></span>
                            </div>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm">Ubah</button>
                    </div>
                </form>    
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        $(document).ready(function() {

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

            // Load Kota
            // MAKA KITA MEMANGGIL FUNGSI LOADCITY() DAN LOADDISTRICT()
            // AGAR SECARA OTOMATIS MENGISI SELECT BOX YANG TERSEDIA
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

                                var el = $('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>');
                                //KEMUDIAN KITA MASUKKAN VALUE SELECTED DI ATAS KE DALAM TAG OPTION
                                $('#city_id').append(el)
                                resolve()
                            })
                        }
                    });
                })
            }

            //CARA KERJANYA SAMA SAJA DENGAN FUNGSI DI ATAS
            function loadDistrict(destination, type) {
                $.ajax({
                    url: "{{ url('/api/district') }}",
                    type: "GET",
                    data: { city_id: destination },
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

            // edit alamat
            $('#settingForm').on('submit', function(e) {
                e.preventDefault(); // Prevent form from submitting traditionally

                var formData = $(this).serializeArray(); // Serialize form data for submission

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengubah alamat ini?',
                    icon: 'question',
                    showCancelButton: true,
                    showConfirmButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonColor: 'green',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Ubah',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('front.update_address') }}",
                            method: "POST",
                            data: formData,
                            beforeSend: function() {
                                $('.loaderArea').block({ 
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
                            complete: function() {
                                $('.loaderArea').unblock();
                            },
                            success: function(response) {
                                $('#ubahAlamatModal').modal('hide');
                                $.toast({
                                    heading: 'Berhasil',
                                    text: response.success,
                                    showHideTransition: 'slide',
                                    icon: 'success',
                                    position: 'top-right',
                                    hideAfter: 3000
                                });
                                setTimeout(function() {
                                    window.location.reload(true);
                                }, 1500);
                            },
                            error: function(xhr, status, error) {
                                let errors = xhr.responseJSON.errors;
                                console.log(errors);
                                let input = xhr.responseJSON.input;
                                console.log(input);

                                // Clear previous errors
                                $('.text-danger').text('');
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

            $('#checkoutForm').on('submit', function(event) {
                console.log('test');
                event.preventDefault(); // Prevent the default form submission

                // Serialize the form data
                var formData = $(this).serialize();

                // Submit the form data using AJAX
                $.ajax({
                    url: $(this).attr('action'), // Get the form action URL
                    method: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('.loaderAreaPurchase').block({ 
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
                        $('.loaderAreaPurchase').unblock();
                        if (response.success) {
                            snap.pay(response.snapToken, {
                                onSuccess: function(result) {
                                    console.log(result);
                                    $.toast({
                                        heading: 'Berhasil',
                                        text: 'Pembayaran Berhasil',
                                        showHideTransition: 'fade',
                                        icon: 'success',
                                        position: 'top-right',
                                        hideAfter: 3000
                                    });
                                    setTimeout(function() {
                                        window.location.href = response.redirect;
                                    }, 1500);
                                },
                                onPending: function(result) {
                                    console.log(result);    
                                    $.toast({
                                        heading: 'Menunggu',
                                        text: 'Pembayaran Belum Selesai, harap selesaikan terlebih dahulu.',
                                        showHideTransition: 'fade',
                                        icon: 'info',
                                        position: 'top-right',
                                        hideAfter: 3000
                                    });
                                    setTimeout(function() {
                                        window.location.href = "{{ route('customer.listPayment') }}";
                                    }, 1500);
                                },
                                onError: function(result) {
                                    console.log(result);
                                    $.toast({
                                        heading: 'Gagal',
                                        text: 'Pembayaran Gagal',
                                        showHideTransition: 'fade',
                                        icon: 'error',
                                        position: 'top-right',
                                        hideAfter: 3000
                                    });
                                },
                                onClose: function() {
                                    $.toast({
                                        heading: 'Pemberitahuan',
                                        text: 'Anda menutup popup tanpa menyelesaikan pembayaran',
                                        showHideTransition: 'fade',
                                        icon: 'warning',
                                        position: 'top-right',
                                        hideAfter: 3000
                                    });
                                    setTimeout(function() {
                                        window.location.href = "{{ route('customer.listPayment') }}";
                                    });
                                }
                            });
                        } else {
                            // Display error toast
                            $.toast({
                                heading: 'Gagal',
                                text: response.error,
                                showHideTransition: 'slide',
                                icon: 'error',
                                position: 'top-right',
                                hideAfter: 3000
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $('.loaderAreaPurchase').unblock();
                        var errorMessage = 'Unexpected error occurred.';
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                            }
                        } catch (e) {
                            errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + xhr.responseText;
                        }
                        if(xhr.status == 400) {
                            $.toast({
                                heading: 'Gagal',
                                text: errorMessage,
                                showHideTransition: 'fade',
                                icon: 'warning',
                                position: 'top-right',
                                hideAfter: 3000
                            });
                            setTimeout(function() {
                                window.location.reload(true);
                            }, 1500)
                        } else {
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
                            }, 1500)
                        }
                    }
                });
            });
        });

        function pilihkurir(kurir, weight, city) {
            var idKurir = '#courier' + kurir
            var idWeight = '#courier' + weight
            var idLayanan = '#service' + kurir
            var value = $(idKurir).val();

            let origin = city;
            let destination = '{{ $customer->district->city->id }}';
            let courier = value;
            let berat = weight;

            if(courier != ""){
                // Define URL
                let url = '{{ route("front.cekOngkir", ["origin" => ":origin", "destination" => ":destination", "weight" => ":weight", "courier" => ":courier"])}}';

                url = url.replace(':origin', origin);
                url = url.replace(':destination', destination);
                url = url.replace(':weight', berat);
                url = url.replace(':courier', courier);

                if(courier) {
                    jQuery.ajax({
                        url: url,
                        type:'GET',
                        dataType:'json',
                        beforeSend: function() {
                            $('.loaderAreaService' + kurir).block({ 
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
                        complete: function() {
                            $('.loaderAreaService' + kurir).unblock();
                        },
                        success: function(data){
                            $(idLayanan).empty();
                            $(idLayanan).append('<option value="">Pilih Layanan</option>')
                            if(data.length > 0) {
                                $.each(data, function(key, value){
                                    $.each(value.costs, function(key1, value1){
                                        $.each(value1.cost, function(key2, value2){
                                            $(idLayanan).append('<option value="'+ value1.service + ' ' + '-' + ' ' + value1.description + ' - ' + value2.value.toLocaleString("id-ID", {style:"currency", currency:"IDR", minimumFractionDigits: 0}).replace(/\D00(?=\D*$)/, '') +'">' + value1.service + ' - ' + value1.description + ' - ' + value2.value.toLocaleString("id-ID", {style:"currency", currency:"IDR", minimumFractionDigits: 0}).replace(/\D00(?=\D*$)/, '') +'</option>')
                                        });
                                    });
                                });
                            }
                        },
                        error: function(xhr) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                            }
                            $.toast({
                                heading: 'Error',
                                text: 'Jaringan bermasalah : ' + errorMessage,
                                showHideTransition: 'fade',
                                icon: 'error',
                                position: 'top-right',
                                hideAfter: 3000
                            });
                        }
                    });
                } else {
                    $('#service' + kurir).empty();
                }
            } else {
                $(idKurir).append('<option value="">Pilih Kurir</option>');
            }
        }

        function pilihlayanan(layanan, weight) {
            var idLayanan = '#service' + layanan
            var idCourier = '#courier' + layanan
            var ongkir = '#ongkir' + layanan

            // set format rupiah for ongkos kirim
            var selectedService = $('#service' + layanan).val();
            var serviceDetails = selectedService.split(' - ');
            var serviceCost = parseInt(serviceDetails[2].replace(/Rp\s?|[,.]/g, '').trim());
            
            const setFormatOngkosKirim = new Intl.NumberFormat("id-ID", {
                style: "currency", 
                currency: "IDR",
                maximumSignificantDigits: "3"
            }).format(serviceCost)
            $(ongkir).val(setFormatOngkosKirim)

            // push value service to array
            let valueService = [];
            var z = 1;
            while ($("#service" + z).length) {
                valueService.push($("#service" + z).val());
                z++;
            }

            // push value courier to array
            let valueCourier = [];
            var x = 1;
            while ($("#courier" + x).length) {
                valueCourier.push($("#courier" + x).val());
                x++;
            }

            let valuesArray = [];
        
            function cleanAndConvertCurrency(value) {
                // hapus Rp dan semua non-digit karakter, kemudian konversi ke bilangan bulat
                let numericValue = parseFloat(value.replace(/Rp\s?|[.,]/g, '').replace(/(,)/g, '.'));
                return isNaN(numericValue) ? 0 : numericValue;
            }

            let index = 1;
            while ($("#ongkir" + index).length > 0) {
                let valueText = $("#ongkir" + index).val();
                let numericValue = cleanAndConvertCurrency(valueText);
                valuesArray.push(numericValue);
                index++;
            }

            let sum = valuesArray.reduce(function(accumulator, currentValue) {
                return accumulator + currentValue;
            }, 0);

            var inputCost = $('#product_count');
            valuesArray.forEach((value, i) => {
                let inputId = 'cost' + (i + 1);
                let input = $('#' + inputId);

                if (input.length === 0) {
                    inputCost.append('<input type="hidden" id="' + inputId + '" name="cost[]" value="' + value + '"/>');
                } else {
                    input.val(value);
                }
            });

            // product id
            let arrayProductIds = [];
            $("input[name='product_id[]']").each(function(index) {
                arrayProductIds.push($(this).val());
            });

            let arraySellerIds = [];
            $("input[name='seller_id[]']").each(function(index) {
                arraySellerIds.push($(this).val());
            });
            
            // kuantiti
            let arrayQty = [];
            $("input[name='qty[]']").each(function(index) {
                arrayQty.push($(this).val());
            });

            // berat
            let arrayweight = [];
            $("input[name='weight[]']").each(function(index) {
                arrayweight.push($(this).val());
            });

            // Send AJAX request to update cart
            $.ajax({
                url: '{{ route("front.update_cart") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    seller_id: arraySellerIds,
                    product_id: arrayProductIds,
                    qty: arrayQty,
                    weight: arrayweight,
                    cost: valuesArray,
                    courier: valueCourier,
                    service: valueService,
                },
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
                complete: function () {
                    $.unblockUI();
                },
                success: function(response) {
                    // cek response
                    if(response.success) {
                        $.toast({
                            heading: 'Berhasil',
                            text: response.success,
                            showHideTransition: 'slide',
                            icon: 'success',
                            position: 'top-right',
                            hideAfter: 3000
                        });
                        setTimeout(function(){
                            window.location.reload(true);
                        }, 1500)
                    } else {
                        $.toast({
                            heading: 'Gagal',
                            text: response.error,
                            showHideTransition: 'fade',
                            icon: 'warning',
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
                }
            });
        }
    </script>
@endsection

@section('css')
    <style>
        .input-error {
            border: 1px solid red;
        }

        .custom-select-container {
            position: relative;
            width: 100%;
        }

        .custom-select-options {
            display: none;
            background: white;
            padding: 10px 10px 0 10px;
            margin-top: 10px;
        }

        .option-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .option-item img.small-img {
            width: 100px;
            object-fit: contain;
            height: auto;
            margin-right: 10px;
        }

        .pilih-button, .remove-button {
            margin-left: 10px;
        }

        .option-item:last-child {
            border-bottom: none;
        }

        .pilih-button {
            margin-left: 10px;
        }
    </style>
@endsection