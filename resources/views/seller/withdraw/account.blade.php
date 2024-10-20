@extends('layouts.seller')

@section('title')
    <title>Pilih Rekening</title>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container">
                <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0 text-dark">Penarikan Dana</h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Dashboard</a></li> --}}
                        <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Penarikan Dana</a></li>
                        <li class="breadcrumb-item active">Tambah Rekening</li>
                    </ol>
                </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container">
                @if (session('success'))
                    <input type="hidden" id="success-message" value="{{ session('success') }}">
                @endif

                @if (session('error'))
                    <input type="hidden" id="error-message" value="{{ session('error') }}">
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tambah Rekening</h4>
                    </div>
                    <form action="{{ route('seller.postWithdraw') }}" id="postWithdraw" method="POST">
                        @csrf
                        <div class="card-body loader-area">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Pilih Bank</label>
                                        <select name="bank_name" id="bank_name" class="form-control">
                                            <option value="">Pilih Bank</option>
                                            <option value="bca">BCA</option>
                                            <option value="bri">BRI</option>
                                            <option value="bni">BNI</option>
                                            <option value="mandiri">Mandiri</option>
                                            <option value="btpn">BTPN</option>
                                            <option value="cimb_niaga">CIMB Niaga</option>
                                            <option value="danamon">Danamon</option>
                                            <option value="dki">DKI</option>
                                            <option value="ocbc">OCBC</option>
                                        </select>
                                        <span class="text-danger" id="bank_name_error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Masukkan Nomor Rekening</label>
                                        <input type="text" name="account_number" id="account_number" placeholder="Masukkan Nomor Rekening" class="form-control">
                                        <span class="text-danger" id="account_number_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-sm float-right">Tambah Rekening</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Daftar Rekening</h4>
                    </div>
                    <div class="card-body loader-area-1">
                        @if($datas->count() > 0)
                            <form id="selectBankAccountForm" method="POST">
                                @csrf  <!-- Include the CSRF token for security -->
                                <div class="row g-4">
                                    @foreach ($datas as $data)
                                        <div class="col-md-3 mb-4">
                                            <div class="card h-100 me-3">
                                                <div class="card-body text-center">
                                                    <input type="radio" id="account{{ $data->id }}" name="bank_account" value="{{ $data->id }}" class="form-check-input">
                                                    <label for="account{{ $data->id }}" class="w-100">
                                                        <div class="mb-3">
                                                            <img src="{{ $data->bank_name_image }}" alt="{{ $data->bank_name }} Logo" class="img-fluid bank-logo mb-3" style="width: 60px;">
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <h5 class="card-title">{{ $data->bank_name_label }}</h5>
                                                            <div class="d-flex align-items-center justify-content-between mt-4">
                                                                <div style="height: 50px; width: 50px; display: block; border: 1px solid #ededed;">
                                                                    <img src="{{ $data->bank_name_image }}" alt="{{ $data->account_holder }}" style="height: 100%; width: 100%; object-fit: contain;">
                                                                </div>
                                                                <div class="d-flex flex-column ml-2">
                                                                    <span class="account-holder">{{ $data->account_name }}</span>
                                                                    <span class="account-number">{{ $data->formatted_account_number }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <hr>
                                <div class="float-right">
                                    <button class="btn btn-danger btn-md mr-1" id="getAccount">Hapus Rekening <i class="fa fa-trash ml-1"></i></button>
                                    <button type="submit" id="selectAccountButton" class="btn btn-success btn-md">Pilih Rekening <i class="fas fa-check ml-1"></i></button>
                                </div>
                            </form>
                        @else
                            <h5 class="text-center">Tidak ada rekening</h5>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- Modal Return Form -->
    <div class="modal fade" id="accountModal" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%;" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <div class="ml-2">
                        <h5 class="modal-title" id="accountModalLabel">Hapus Rekening</h5>
                    </div>
                </div>
                <form id="removeAccount" type="post">
                    @csrf
                    <input type="hidden" name="account_id" id="accountId" value="">
                    <div class="modal-body" style="padding: 15px 25px">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <th width="30%">Nama Pelanggan</th>
                                        <td id="accountName"></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Nomor Rekening</th>
                                        <td id="accountNumber"></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">BANK</th>
                                        <td id="bankName"></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">Tanggal Ditambahkan</th>
                                        <td id="createdAt"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-md mr-1" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-md mr-2">Hapus Rekening</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>

        $(document).ready(function (){

            // Intercept form submit event
            $('#selectBankAccountForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the traditional form submission

                // Get the selected radio input (bank account)
                var selectedAccount = $('input[name="bank_account"]:checked').val();

                // Send AJAX request to server
                $.ajax({
                    url: "{{ route('withdrawals.select') }}", // Your route for the account selection
                    type: 'POST',
                    data: {
                        account_id: selectedAccount,
                        _token: '{{ csrf_token() }}'
                    }, // Serialize the form data (includes CSRF token and selected account)\
                    beforeSend: function() {
                        // Show loader
                        $('.loader-area-1').block({
                            message: '<i class="fa fa-spinner fa-spin"></i>',
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
                        // Unblock the UI loader
                        $('.loader-area-1').unblock();

                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: function() {
                                window.location.href = "{{ route('withdrawals.index') }}"; // Redirect upon success
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        // Unblock the UI loader
                        $('.loader-area-1').unblock();
                        var response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                        }

                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error',
                            timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // get account
            $('#getAccount').on('click', function(e){
                e.preventDefault();
                // Get the selected radio input (bank account)
                var id = $('input[name="bank_account"]:checked').val();
                var url = "{{ route('withdrawals.detailAccount', ['id' => ':id']) }}";
                url = url.replace(':id', id);

                if(!id){
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Harap pilih rekening yang ingin dihapus terlebih dahulu',
                        icon: 'error',
                        timer: 2000,
                        showCancelButton: false,
                        showConfirmButton: false,
                    });
                }

                $.ajax({
                    url: url,
                    method: "GET",
                    beforeSend: function() {
                        $('.loader-area-1').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i>', 
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
                        $('.loader-area-1').unblock();
                    },
                    success: function(response) {
                        // Populate modal fields with response data
                        $('#accountId').val(response.account_id);
                        $('#accountName').text(response.account_name);
                        $('#accountNumber').text(response.account_number);
                        $('#bankName').text(response.bank_name);
                        $('#createdAt').text(response.created_at);

                        // Show the modal
                        $('#accountModal').modal('show');
                    }
                });
            });

            $('#removeAccount').on('submit', function(event){
                event.preventDefault();
                
                // close modal
                $('#accountModal').modal('hide');

                // set url
                var accountId = $('#accountId').val();
                var url = "{{ route('withdrawals.removeAccount', ['id' => ':id']) }}";
                url = url.replace(':id', accountId);

                // show popup confirm
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus rekening ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            beforeSend: function() {
                                $('.loader-area-1').block({ 
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
                            success: function(response) {
                                $('.loader-area-1').unblock();
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: response.success,
                                    icon: 'success',
                                    timer: 1500, // Display for 2 seconds
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        window.location.reload(true);
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                $('.loader-area-1').unblock();
                                var response = JSON.parse(xhr.responseText);
                                if (response.error) {
                                    errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                }
                                Swal.fire({
                                    title: 'Error',
                                    text: errorMessage,
                                    icon: 'error',
                                    timer: 2000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                });
                            }
                        });
                    }
                });
            });

            $('#postWithdraw').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('seller.postWithdraw') }}",
                    method: "POST",
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
                    success: function(response) {
                        $('.loader-area').unblock();
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success',
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.reload(true);
                            }
                        });
                    },
                    error: function(xhr, status) {
                        $('.loader-area').unblock();
                        let errors = xhr.responseJSON.errors;
                        let input = xhr.responseJSON.input;
                        console.log(errors);
                        console.log(input);

                        // Clear previous errors
                        $('.text-danger').text('');

                        var response = JSON.parse(xhr.responseText);
                        console.log(response);
                        if (response.error) {
                            errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                        }
                        Swal.fire({
                            title: 'Gagal',
                            text: errorMessage,
                            icon: 'error',
                            timer: 3000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                if(xhr.status == 500 || xhr.status == 409){
                                    window.location.reload(true);
                                } else {
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

        /* Optional custom styles */
        .bank-logo {
            max-width: 100px;
        }

        .card-body {
            position: relative;
        }

        input[type="radio"] {
            position: absolute;
            top: 15px;
            right: 15px;
            transform: scale(1.5);
            cursor: pointer;
            opacity: 0.7;
        }

        input[type="radio"]:checked {
            opacity: 1;
            border-color: #5cb85c;
        }
    </style>
@endsection