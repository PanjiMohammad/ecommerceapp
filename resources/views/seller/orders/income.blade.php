@extends('layouts.seller')

@section('title')
    <title>Pendapatan Saya</title>
@endsection

@section('content')

    <!-- Set Padding & Margin on DataTables -->
    <style>
        table.dataTable ul {
            padding-left: 20px;
            margin: 0; /* Reset default margin */
        }

        table.dataTable ul li {
            margin-bottom: 5px;
        }
    </style>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Pendapatan Saya</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('seller.dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pendapatan Saya</li>
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
						<input type="hidden" id="success-message" value="{{ session('success') }}">
					@endif
		
					@if (session('error'))
						<input type="hidden" id="error-message" value="{{ session('error') }}">
					@endif
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST PRODUCT  -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body loader-area">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active show" id="ongoing-tab" data-toggle="tab" href="#ongoing" role="tab" aria-controls="ongoing" aria-selected="true">
                                            <i class="fa-solid fa-rotate"></i> Produk Sedang Berjalan
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-success" id="finish-tab" data-toggle="tab" href="#finish" role="tab" aria-controls="finish" aria-selected="false">
                                            <i class="fa-regular fa-circle-check"></i> Produk Selesai
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-warning" id="return-tab" data-toggle="tab" href="#return" role="tab" aria-controls="return" aria-selected="false">
                                            <i class="fa-solid fa-rotate-right"></i> Produk yang dikembalikan
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-danger" id="cancel-tab" data-toggle="tab" href="#cancel" role="tab" aria-controls="cancel" aria-selected="false">
                                            <i class="fa-regular fa-circle-xmark"></i> Produk yang dibatalkan
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab" style="color: black">
                                        <div class="mt-3">
                                            <p>Total Pendapatan : <span class="font-weight-bold">{{ 'Rp ' . number_format($ongoing, 0, ',', '.') }}</span> dari <span class="font-weight-bold">{{ $ongoingCount }}</span> item yang terjual.</p>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="onGoingTable" class="table table-hover table-ongoing">
                                                <thead>
                                                    <tr>
                                                        <th style="padding: 10px 10px;">#</th>
                                                        <th style="padding: 10px 10px;">Produk</th>
                                                        <th style="padding: 10px 10px;">Qty</th>
                                                        <th style="padding: 10px 10px;">Harga</th>
                                                        <th style="padding: 10px 10px;">Status</th>
                                                        <th style="padding: 10px 10px;">Subtotal</th>
                                                        <th style="padding: 10px 10px;">Nomor Resi</th>
                                                        <th style="padding: 10px 10px;">Layanan</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="finish" role="tabpanel" aria-labelledby="finish-tab">
                                        <div class="mt-3">
                                            <p>Total Pendapatan : <span class="font-weight-bold">{{ 'Rp ' . number_format($finish, 0, ',', '.') }}</span> dari <span class="font-weight-bold">{{ $finishCount }}</span> item yang terjual.</p>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="finishTable" class="table table-hover table-finish">
                                                <thead>
                                                    <tr>
                                                        <th style="padding: 10px 10px;">#</th>
                                                        <th style="padding: 10px 10px;">Produk</th>
                                                        <th style="padding: 10px 10px;">Qty</th>
                                                        <th style="padding: 10px 10px;">Harga</th>
                                                        <th style="padding: 10px 10px;">Status</th>
                                                        <th style="padding: 10px 10px;">Subtotal</th>
                                                        <th style="padding: 10px 10px;">Nomor Resi</th>
                                                        <th style="padding: 10px 10px;">Layanan</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="return" role="tabpanel" aria-labelledby="return-tab">
                                        {{-- <div class="mt-3">
                                            <p>Total  : <span class="font-weight-bold">{{ 'Rp ' . number_format($cancel, 0, ',', '.') }}</span> dari <span class="font-weight-bold">{{ $cancelCount }}</span> pesanan yang diba.</p>
                                        </div> --}}
                                        <div class="table-responsive mt-2">
                                            <table id="returnTable" class="table table-hover table-return">
                                                <thead>
                                                    <tr>
                                                        <th style="padding: 10px 10px;">#</th>
                                                        <th style="padding: 10px 10px;">Produk</th>
                                                        <th style="padding: 10px 10px;">Qty</th>
                                                        <th style="padding: 10px 10px;">Nominal Pengembalian</th>
                                                        <th style="padding: 10px 10px;">Nomor Resi</th>
                                                        <th style="padding: 10px 10px;">Alasan Pengembalian</th>
                                                        <th style="padding: 10px 10px;">Status</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="cancel" role="tabpanel" aria-labelledby="cancel-tab">
                                        {{-- <div class="mt-3">
                                            <p>Total  : <span class="font-weight-bold">{{ 'Rp ' . number_format($cancel, 0, ',', '.') }}</span> dari <span class="font-weight-bold">{{ $cancelCount }}</span> pesanan yang diba.</p>
                                        </div> --}}
                                        <div class="table-responsive mt-2">
                                            <table id="incomeCancelTable" class="table table-hover table-cancel">
                                                <thead>
                                                    <tr>
                                                        <th style="padding: 10px 10px;">#</th>
                                                        <th style="padding: 10px 10px;">Produk</th>
                                                        <th style="padding: 10px 10px;">Qty</th>
                                                        <th style="padding: 10px 10px;">Harga</th>
                                                        <th style="padding: 10px 10px;">Status</th>
                                                        <th style="padding: 10px 10px;">Subtotal</th>
                                                        <th style="padding: 10px 10px;">Layanan</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY  -->
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- IMPORTANT LINK -->
    <a href="{{ route('orders.incomesGetDatatables') }}" id="incomeOnGoingData"></a>
    <a href="{{ route('orders.incomesFinishGetDatatables') }}" id="incomeFinishData"></a>
    <a href="{{ route('orders.incomesCancelGetDatatables') }}" id="incomeCancelData"></a>
    <a href="{{ route('orders.incomesReturnGetDatatables') }}" id="incomeReturnData"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        $(document).ready(function(){

            // session
            var successMessage = $('#success-message').val();
            var errorMessage = $('#error-message').val();

            if (successMessage) {
                Swal.fire({
                    title: 'Berhasil',
                    text: successMessage,
                    icon: 'success',
                    timer: 2000,
                    showCancelButton: false,
                    showConfirmButton: false,
                    willClose: () => {
                        table.ajax.reload();
                    }
                });
            }

            if (errorMessage) {
                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error',
                    timer: 2000,
                    showCancelButton: false,
                    showConfirmButton: false,
                    willClose: () => {
                        table.ajax.reload();
                    }
                });
            }

            // on going datatables
            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Pesanan...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada pesanan'
                },
                initComplete: function() {
                    // Add Bootstrap form-control class to search input
                    var $searchInput = $('#onGoingTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Pesanan...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    // Add Bootstrap form-control class to length menu dropdown
                    var $lengthMenu = $('#onGoingTable_length select').addClass('form-control form-control-sm');

                    // Add display flex and align-items-center to the parent of the length menu
                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    // Ensure the 'Tampil' label is vertically aligned
                    $('#onGoingTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#incomeOnGoingData').attr('href');
            var table = $('#onGoingTable').DataTable({
                ajax: {
                    url: url,
                    beforeSend: function() {
                        $('.table-ongoing').block({ 
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
                        $('.table-ongoing').unblock();
                    }
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = table.page.info();
                    var value = index + 1 + info.start + '.';
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false, className: 'text-center align-items-center align-middle'}, 
                    {data: 'productName', name: 'productName', className: 'align-middle'},
                    {data: 'qty', name: 'qty', className: 'align-middle'}, 
                    {data: 'price', name: 'price', className: 'align-middle'}, 
                    {data: 'status', name: 'status', className: 'align-middle'},
                    {data: 'subtotal', name: 'subtotal', className: 'align-middle'},
                    {data: 'tracking_number', name: 'tracking_number', className: 'align-middle'},
                    {data: 'shipping_service', name: 'shipping_service', className: 'align-middle'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // finish datatables
            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Pesanan...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada pesanan'
                },
                initComplete: function() {
                    // Add Bootstrap form-control class to search input
                    var $searchInput = $('#finishTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Pesanan...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    // Add Bootstrap form-control class to length menu dropdown
                    var $lengthMenu = $('#finishTable_length select').addClass('form-control form-control-sm');

                    // Add display flex and align-items-center to the parent of the length menu
                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    // Ensure the 'Tampil' label is vertically aligned
                    $('#finishTable_length').addClass('d-flex align-items-center');
                }
            });

            var urlFinish = $('#incomeFinishData').attr('href');
            var tableFinish = $('#finishTable').DataTable({
                ajax: {
                    url: urlFinish,
                    beforeSend: function() {
                        $('.table-finish').block({ 
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
                        $('.table-finish').unblock();
                    }
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = tableFinish.page.info();
                    var value = index + 1 + info.start + '.';
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false, className: 'text-center align-items-center align-middle'}, 
                    {data: 'productName', name: 'productName', className: 'align-middle'},
                    {data: 'qty', name: 'qty', className: 'align-middle'}, 
                    {data: 'price', name: 'price', className: 'align-middle'}, 
                    {data: 'status', name: 'status', className: 'align-middle'},
                    {data: 'subtotal', name: 'subtotal', className: 'align-middle'},
                    {data: 'tracking_number', name: 'tracking_number', className: 'align-middle'},
                    {data: 'shipping_service', name: 'shipping_service', className: 'align-middle'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // return datatables
            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Pesanan...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada pesanan'
                },
                initComplete: function() {
                    // Add Bootstrap form-control class to search input
                    var $searchInput = $('#returnTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Pesanan...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    // Add Bootstrap form-control class to length menu dropdown
                    var $lengthMenu = $('#returnTable_length select').addClass('form-control form-control-sm');

                    // Add display flex and align-items-center to the parent of the length menu
                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    // Ensure the 'Tampil' label is vertically aligned
                    $('#returnTable_length').addClass('d-flex align-items-center');
                }
            });

            var urlReturn = $('#incomeReturnData').attr('href');
            var tableReturn = $('#returnTable').DataTable({
                ajax: {
                    url: urlReturn,
                    beforeSend: function() {
                        $('.table-return').block({ 
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
                        $('.table-return').unblock();
                    }
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = tableReturn.page.info();
                    var value = index + 1 + info.start + '.';
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false, className: 'text-center align-items-center align-middle'}, 
                    {data: 'productName', name: 'productName', className: 'align-middle'},
                    {data: 'qty', name: 'qty', className: 'align-middle'}, 
                    {data: 'refund_transfer', name: 'refund_transfer', className: 'align-middle'},
                    {data: 'tracking_number', name: 'tracking_number', className: 'align-middle'},
                    {data: 'reason', name: 'reason', className: 'align-middle text-capitalize'},
                    {data: 'status', name: 'status', className: 'align-middle'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // cancel datatables
            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Pesanan...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada pesanan'
                },
                initComplete: function() {
                    // Add Bootstrap form-control class to search input
                    var $searchInput = $('#incomeCancelTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Pesanan...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    // Add Bootstrap form-control class to length menu dropdown
                    var $lengthMenu = $('#incomeCancelTable_length select').addClass('form-control form-control-sm');

                    // Add display flex and align-items-center to the parent of the length menu
                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    // Ensure the 'Tampil' label is vertically aligned
                    $('#incomeCancelTable_length').addClass('d-flex align-items-center');
                }
            });

            var urlCancel = $('#incomeCancelData').attr('href');
            var tableCancel = $('#incomeCancelTable').DataTable({
                ajax: {
                    url: urlCancel,
                    beforeSend: function() {
                        $('.table-cancel').block({ 
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
                        $('.table-cancel').unblock();
                    }
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = tableCancel.page.info();
                    var value = index + 1 + info.start + '.';
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false, className: 'text-center align-items-center align-middle'}, 
                    {data: 'productName', name: 'productName', className: 'align-middle'},
                    {data: 'qty', name: 'qty', className: 'align-middle'}, 
                    {data: 'price', name: 'price', className: 'align-middle'}, 
                    {data: 'status', name: 'status', className: 'align-middle'},
                    {data: 'subtotal', name: 'subtotal', className: 'align-middle'},
                    {data: 'shipping_service', name: 'shipping_service', className: 'align-middle'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

        });
    </script>
@endsection
