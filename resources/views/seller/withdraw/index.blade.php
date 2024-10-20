@extends('layouts.seller')

@section('title')
    <title>Penarikan Dana</title>
@endsection

@section('content')

    <style>
        table.dataTable ul {
            padding-left: 20px;
            margin: 0; /* Reset default margin */
        }

        table.dataTable ul li {
            margin-bottom: 5px;
        }

        ul {
            padding-left: 20px;
            margin: 0;
        }

        ul li {
            margin-bottom: 5px;
        }
    </style>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container">
                <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0 text-dark">Penarikan Dana</h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Penarikan Dana</li>
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
                    <input type="hidden" id="success-message" value="{{ session('success') }}">
                @endif

                @if (session('error'))
                    <input type="hidden" id="error-message" value="{{ session('error') }}">
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Daftar Penarikan Dana</h4>
                        {{-- <div class="dropdown float-right">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Opsi
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                
                            </div>
                        </div> --}}

                        <!-- 
                            <div class="float-right">
                                <a class="btn btn-primary btn-sm" href="#" data-toggle="modal" data-target="#withdrawalModal">
                                    Ajukan Penarikan <i class="fas fa-plus ml-2"></i>
                                </a>
                                {{-- <div class="dropdown-divider"></div> --}}
                                <a class="btn btn-success btn-sm ml-1" href="#" data-toggle="modal" data-target="#downloadReportModal">
                                    Unduh Laporan <i class="fas fa-download ml-2"></i>
                                </a>
                            </div>
                        -->
                    </div>

                    @php
                        // Get the selected account details from the authenticated user
                        $seller = auth()->guard('seller')->user();
                        $account = $seller->selected_account_id ? \App\SellerBankAccount::find($seller->selected_account_id) : null;


                        // dd($account);
                        // function formatAccountNumber($number) {
                        //     if ($number) {
                        //         $length = strlen($number);
                        //         return substr($number, 0, 4) . str_repeat('*', $length - 4);
                        //     }
                        //     return '-';
                        // }

                        function formatAccountNumber($number) {
                            if ($number) {
                                return str_replace(substr($number, 0, 4), '****', $number);
                            }
                            return '-';
                        }
                    @endphp
                    <div class="card-body loader-area">
                        @if($account)
                            <div class="mb-4">
                                <p>Informasi Saldo</p>
                                <div style="border: 1px solid #ededed; border-radius: 4px; padding: 15px;">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="mt-5 mb-5">
                                                <div class="d-flex align-middle">
                                                    <span>
                                                        <span style="font-size: 30px;">{{ 'Rp ' . number_format($activeAmount, 0, ',', '.') }}</span>
                                                        @if($activeAmount > 0)
                                                            <a href="#" data-toggle="modal" data-target="#withdrawalModal" class="btn btn-primary ml-2 mb-2">Tarik Dana</a>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="mt-2" style="background-color: #ededed; padding: 4px 10px;">
                                                    <ul>
                                                        <li><small class="text-dark">Saldo aktif {{ 'Rp ' . number_format($activeAmount, 0, ',', '.') }}</small></li>
                                                        <li><small class="text-dark">Saldo tidak aktif {{ 'Rp ' . number_format($nonActiveAmount, 0, ',', '.') }}</small></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-md-5 justify-content-end">
                                            <div class="d-flex align-items-center">
                                                <span>Rekening Bank Saya</span>
                                                <a href="{{ route('withdrawals.account') }}" class="ml-4">Lainnya <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></a>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center">
                                                    <div style="height: 60px; width: 60px; display: block; border: 1px solid #ededed; border-radius: 4px;" class="mt-2">
                                                        <img src="{{ $account ? $account->bank_name_image : asset('ecommerce/img/logo/default.png') }}" 
                                                            alt="{{ $account->bank_name_label }}" style="height: 100%; width: 100%; object-fit: contain;">
                                                    </div>
                                                    <div class="d-flex flex-column ml-2">
                                                        <span>
                                                            <span>{{ $account->bank_name_label ?? '-' }}</span>
                                                            <span class="badge badge-success ml-2">utama</span>
                                                            <span class="ml-2">{{ formatAccountNumber($account->account_number) }}</span>
                                                        </span>
                                                        <small style="color: #777" class="mt-1">Telah Ditambahkan</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mb-4">
                                <p>Informasi Saldo</p>
                                <div style="border: 1px solid #777; border-radius: 4px; padding: 15px;">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-middle">
                                                <span>
                                                    <span style="font-size: 30px;">{{ 'Rp ' . number_format($activeAmount, 0, ',', '.') }}</span>
                                                    @if($activeAmount > 0)
                                                        <a href="#" data-toggle="modal" data-target="#withdrawalModal" class="btn btn-primary ml-2 mb-2">Tarik Dana</a>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="mt-2" style="background-color: #ededed; padding: 4px 10px;">
                                                <small class="text-dark">Saldo aktif {{ 'Rp ' . number_format($activeAmount, 0, ',', '.') }}</small>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                <span>Rekening Bank Saya</span>
                                                <a href="{{ route('withdrawals.account') }}" class="ml-4">Lainnya <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i></a>
                                            </div>
                                            <div class="mt-3">
                                                <div class="d-flex align-items-center">
                                                    <span><i class="fa-solid fa-square-xmark"></i> Belum ada akun yang dipilih</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="d-flex mb-4 mt-4">
                            {{-- <span class="font-weight-bold">Laporan : </span> --}}
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center mr-1">
                                    <span class="font-weight-bold mr-2">Pilih Tanggal : </span>
                                    <div id="created_at" class="pull-right" style="background: #fff; cursor: pointer; padding: 3px 10px; border: 1px solid #ccc; border-radius: 4px; width: auto;">
                                        <i class="fa-regular fa-calendar-days"></i>&nbsp;
                                        <span></span> <b class="caret"></b>
                                    </div>
                                </div>
                                <div id="downloadButtons" style="display: none;">
                                    <a target="_blank" href="javascript:void(0);" class="btn btn-primary btn-sm ml-1" id="downloadPdf" title="Export File PDF">
                                        Export PDF <i class="fa-regular fa-file-pdf ml-1"></i>
                                    </a>
                                    <a target="_blank" href="javascript:void(0);" class="btn btn-info btn-sm ml-1" id="downloadExcel" title="Download File Excel">
                                        Export Excel <i class="fa-solid fa-file-excel ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <table class="table" id="tableWithdrawal">
                            <thead>
                                <tr>
                                    <th style="padding: 10px 10px;">#</th>
                                    <th style="padding: 10px 10px;">Tanggal</th>
                                    <th style="padding: 10px 10px;">Nama Akun</th>
                                    <th style="padding: 10px 10px;">Jumlah Dana</th>
                                    <th style="padding: 10px 10px;">Bank</th>
                                    <th style="padding: 10px 10px;">Nomor Rekening</th>
                                    <th style="padding: 10px 10px;">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Ajukan Penarikan Form -->
            <div class="modal fade" id="withdrawalModal" tabindex="-1" role="dialog" aria-labelledby="withdrawalModalLabel" aria-hidden="true">
                <div class="modal-dialog" style="max-width: 70%;" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="withdrawalModalLabel">Tarik Dana</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="withdrawalForm" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body loader-area-withdrawal">
                                <!-- Tarik Dana Ke Section -->
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-4 text-md-right">
                                        <span>Tarik Dana Ke</span>
                                    </div>
                                    <div class="col-md-5">
                                        <div style="padding: 7px 10px; border: 1px solid #ededed; border-radius: 4px;" class="d-flex align-items-center">
                                            <input type="radio" id="withdrawalRadio" name="withdrawal_id" value="{{ $account->id ?? null }}" checked>
                                            <div style="height: 60px; width: 60px; display: block; border: 1px solid #ededed; border-radius: 4px;" class="ml-2">
                                                <img src="{{ $account ? $account->bank_name_image : asset('ecommerce/img/logo/default.png') }}" 
                                                    alt="{{ $account->bank_name_label ?? '' }}" style="height: 100%; width: 100%; object-fit: contain;">
                                            </div>
                                            <div class="d-flex flex-column ml-3">
                                                <span>Rekening Bank</span>
                                                <span>
                                                    <span>{{ $account->bank_name_label ?? '' }}</span>
                                                    <span class="badge badge-success ml-2">utama</span>
                                                    <span class="ml-2">{{ formatAccountNumber($account->account_number ?? '') }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <!-- Jumlah Penarikan Dana Section -->
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-4 text-md-right">
                                        <span>Jumlah Penarikan Dana</span>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                            </div>
                                            <input type="text" id="withdrawalNominal" name="withdrawalNominal" class="form-control" placeholder="Masukkan nominal" aria-label="withdrawalNominal" aria-describedby="basic-addon1">
                                        </div>
                                        <ul style="padding-left: 20px; margin: 0;">
                                            <li>Tarik saldo saat ini: {{ 'Rp ' . number_format($activeAmount, 0, ',', '.') }}</li>
                                            <li>Batas Penarikan Dana: {{ 'Rp ' . number_format(18000000, 0, ',', '.') }}</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="row align-items-center">
                                    <div class="col-md-4 text-md-right">
                                        <span>Biaya Transaksi <i class="fa-regular fa-circle-question"></i></span>
                                    </div>
                                    <div class="col-md-4">
                                        <span>Rp 0</span>
                                    </div>
                                </div>
                            </div>                                              
                            <div class="modal-footer d-flex justify-content-between align-items-center">
                                <!-- Left side content -->
                                <div>
                                    <span>Kuota penarikan gratis minggu ini : {{ $countWithdrawal }} / 9999</span>
                                </div>
                            
                                <!-- Right side content -->
                                <div class="d-flex align-items-center">
                                    <div class="d-flex flex-column text-right">
                                        <small>Jumlah Akhir Penarikan Dana</small>
                                        <span id="nominalEnd">Rp 0</span>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-md ml-2 confirm-button" disabled>Konfirmasi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Unduh Laporan -->
            <div class="modal fade" id="downloadReportModal" tabindex="-1" aria-labelledby="downloadReportModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="downloadReportModalLabel">Unduh Laporan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="reportForm">
                                @csrf
                                <div class="form-group">
                                    <label for="reportType">Jenis Laporan</label>
                                    <input type="text" class="form-control" name="reportType" value="Laporan Penarikan Dana" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="dateLabel">Pilih Tanggal</label>
                                    <div id="created_at" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; border-radius: 5px; width: 100%">
                                        <i class="fa-regular fa-calendar-days"></i>&nbsp;
                                        <span></span> <b class="caret"></b>
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

    <!-- IMPORTANT LINK -->
    <a href="{{ route('withdrawals.getDatatables') }}" id="withdrawalsGetData"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            // session
            var successMessage = $('#success-message').val();
            var errorMessage = $('#error-message').val();

            if (successMessage) {
				Swal.fire({
                    title: 'Berhasil',
                    text: successMessage,
                    icon: 'success',
                    timer: 1500,
                    showCancelButton: false,
                    showConfirmButton: false,
                    willClose: () => {
                        Swal.close();
                    }
                });
            }

            if (errorMessage) {
				Swal.fire({
                    title: 'Berhasil',
                    text: errorMessage,
                    icon: 'success',
                    timer: 1500,
                    showCancelButton: false,
                    showConfirmButton: false,
                    willClose: () => {
                        window.location.reload(true);
                    }
                });
            }

            // Download report
            let start = moment().startOf('month')
            let end = moment().endOf('month')

            function updateExportLink(start, end) {
                $('#downloadPdf').data('href', '/seller/withdraw/' + start.format('YYYY-MM-DD') + '+' + end.format('YYYY-MM-DD'));
                $('#downloadExcel').data('href', '{{ route("withdraw.excel", ":daterange") }}'
                .replace(':daterange', start.format('YYYY-MM-DD') + '+' + end.format('YYYY-MM-DD')));
            }

            updateExportLink(start, end);

            function cb(start, end) {
                $('#created_at span').html(start.locale('id').format('dddd, DD MMMM YYYY') + ' - ' + end.locale('id').format('dddd, DD MMMM YYYY'));
            }

            $('#created_at').daterangepicker({
                startDate: start,
                endDate: end,
                locale: {
                    format: 'dddd, DD MMMM YYYY',
                    applyLabel: "Terapkan",
                    cancelLabel: "Batal",
                    customRangeLabel: "Custom Tanggal",
                    daysOfWeek: [
                        "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"
                    ],
                    monthNames: [
                        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                    ],
                    firstDay: 1
                },
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
            }, updateExportLink, cb);

            cb(start, end);

            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada data penarikan dana'
                },
                initComplete: function() {
                    var $searchInput = $('#tableWithdrawal_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari...');
                    $searchInput.parent().addClass('d-flex align-items-center float-right');
                    
                    var $lengthMenu = $('#tableWithdrawal_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#tableWithdrawal_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#withdrawalsGetData').attr('href');
            var table = $('#tableWithdrawal').DataTable({
                ajax: {
                    url: url,
                    data: function(d) {
                        d.start_date = $('#created_at').data('daterangepicker').startDate.format('dddd, D MMMM YYYY');
                        d.end_date = $('#created_at').data('daterangepicker').endDate.format('dddd, D MMMM YYYY');
                    },
                    beforeSend: function () {
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
                    complete: function () {
                        $('.loader-area').unblock();
                    },
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = table.page.info();
                    var value = index + 1 + info.start;
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    { data: null, sortable: false, orderable: false, searchable: false },
                    { data: 'formattedDated', name: 'formattedDated' },
                    { data: 'sellerName', name: 'sellerName' },
                    { data: 'amount', name: 'amount', render: $.fn.dataTable.render.number('.', ',', 0, 'Rp ') },
                    { data: 'bankName', name: 'bankName'},
                    { data: 'account_number', name: 'account_number'},
                    { data: 'status', name: 'status', className: 'text-capitalize' },
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // price format
            $('#withdrawalNominal').mask('000.000.000.000.000', {reverse: true});

            $("#withdrawalNominal").on('input', function() {
				var amount = $(this).val();
                console.log(amount);

				if (amount !== '') {
					updateAmount();
                } else {
                    $('.confirm-button').prop('disabled', true);
                    $("#nominalEnd").text('Rp ' + 0);
                }

			});

			function updateAmount() {
				var amount = $("#withdrawalNominal").val();
				if(amount !== 0 && amount !== ''){
					$("#nominalEnd").text('Rp ' + amount);
                    $('.confirm-button').removeAttr('disabled');
				}
			}

            $('#withdrawalForm').on('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Get the form data
                var withdrawalNominal = $('#withdrawalNominal').val(); 
                var accountId = $('#withdrawalRadio').val(); 
                var accountBankPost = '{{ $account->bank_name ?? '' }}';
                var accountNumber = '{{ $account->account_number ?? '' }}';
                
                // Remove non-numeric characters from the withdrawal nominal input
                withdrawalNominal = withdrawalNominal.replace(/[^0-9]/g, '');

                // Validate the withdrawal amount before submission
                if (withdrawalNominal === '' || parseInt(withdrawalNominal) <= 0) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Nominal penarikan tidak boleh kosong atau 0, harap isi nominal',
                        icon: 'error',
                        confirmButtonText: 'Tutup'
                    });
                    return; // Stop if validation fails
                }

                // jika nominal melebihi batas penarikan
                var maxNominal = 18000000;
                if (withdrawalNominal > maxNominal) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Nominal penarikan tidak melebihi batas penarikan yang sudah ditentukan',
                        icon: 'error',
                        confirmButtonText: 'Tutup'
                    });
                    return; // Stop if validation fails
                }

                // Prepare the data to be sent
                var formData = {
                    account_id: accountId,
                    bank_name: accountBankPost,
                    account_number: accountNumber,
                    amount: withdrawalNominal,
                    _token: $('input[name=_token]').val(), // CSRF token
                };

                // URL of the form action
                var actionUrl = "{{ route('withdrawals.update') }}";

                // Confirm withdrawal using SweetAlert
                Swal.fire({
                    title: 'Konfirmasi Penarikan',
                    text: 'Apakah Anda yakin ingin menarik dana ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Make the AJAX request
                        $.ajax({
                            url: actionUrl,
                            method: "POST", // Method is still POST, but we override to PUT using _method
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            beforeSend: function () {
                                // Show loader or block UI
                                $('.loader-area-withdrawal').block({
                                    message: '<i class="fa fa-spinner fa-spin"></i> Proses penarikan...',
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
                                // Unblock UI after the request is done
                                $('.loader-area-withdrawal').unblock();
                            },
                            success: function (response) {
                                $('#withdrawalModal').modal('hide');
                                if (response.success) {
                                    $('#withdrawalNominal').text('');
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.success,
                                        icon: 'success',
                                        timer: 3000,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            // Reload the data table or any required components
                                            table.ajax.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.error,
                                        icon: 'error',
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function (xhr) {
                                var response = JSON.parse(xhr.responseText);
                                if (response.error) {
                                    errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                }
                                Swal.fire({
                                    title: 'Gagal',
                                    text: errorMessage,
                                    icon: 'error',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

            table.on('draw.dt', function() {
                var PageInfo = $('#tableWithdrawal').DataTable().page.info();
                table.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start + '.';
                });
            });

            $('#created_at').on('apply.daterangepicker', function(ev, picker) {
                // Format the start and end dates using moment.locale
                let startDate = moment(picker.startDate).locale(`id`).format('dddd, DD MMMM YYYY');
                let endDate = moment(picker.endDate).locale(`id`).format('dddd, DD MMMM YYYY');
                
                table.ajax.url('/seller/withdraw/getDatatables?date=' + startDate + ' - ' + endDate).load();

                // Update the span text inside the date range picker
                $('#created_at span').html(startDate + ' - ' + endDate);

                // change style none
                $('#downloadButtons').css('display', 'block');

                
                $('#downloadReportModal').modal('hide');
            });

            $('#downloadPdf').on('click', function(e) {
                e.preventDefault();
                const href = $(this).data('href');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mendownload laporan?',
                    icon: 'warning',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Download PDF',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: href,
                            type: 'GET',
                            beforeSend: function() {
                                $('.loader-area').block({ 
                                    message: '<i class="fa fa-spinner fa-spin"></i> Harap tunggu...',
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
                                if(response.success === true) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            window.open(response.file_url, '_blank');
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: response.message,
                                        icon: 'error',
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        timer: 2000,
                                    });
                                }
                            },
                            error: function(xhr) {
                                var response = JSON.parse(xhr.responseText);
                                if (response.error) {
                                    errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                }
                                Swal.fire({
                                    title: 'Gagal',
                                    text: errorMessage,
                                    icon: 'error',
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    timer: 2000,
                                });
                            }
                        });
                    }
                });
            });

            $('#downloadExcel').on('click', function(e) {
                e.preventDefault();
                const href = $(this).data('href');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mendownload laporan?',
                    icon: 'warning',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Download Excel',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: href,
                            type: 'GET',
                            beforeSend: function() {
                                $('.loader-area').block({ 
                                    message: '<i class="fa fa-spinner fa-spin"></i> Harap tunggu...',
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
                                if(response.success === true) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            window.open(response.file_url, '_blank');
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: response.message,
                                        icon: 'error',
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        timer: 2000,
                                    });
                                }
                            },
                            error: function(xhr) {
                                var response = JSON.parse(xhr.responseText);
                                if (response.error) {
                                    errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                }
                                Swal.fire({
                                    title: 'Gagal',
                                    text: errorMessage,
                                    icon: 'error',
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    timer: 2000,
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection