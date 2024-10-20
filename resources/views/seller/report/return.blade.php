@extends('layouts.seller')

@section('title')
    <title>Laporan Pengembalian Pesanan</title>
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
                        <h1 class="m-0 text-dark">Laporan Pengembalian Pesanan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Laporan</li>
                            <li class="breadcrumb-item active">Laporan Pengembalian Pesanan</li>
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
                <div class="row">
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST PRODUCT  -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Daftar Laporan Pengembalian Pesanan</h4>
                            </div>
                            <div class="card-body" id="loaderArea">
                                <div class="d-flex mb-3 justify-content-end">
                                    {{-- <div class="input-group flex-nowrap" style="width: 36%;">
                                        <span class="input-group-text" id="basic-addon1"><span class="fa-regular fa-calendar-days"></span></span>
                                        <input type="text" id="created_at" name="date" class="form-control" aria-describedby="basic-addon1">
                                    </div> --}}
                                    
                                    <div class="d-flex align-items-center mr-1">
                                        <span style="margin-top: 1px;" class="mr-2 font-weight-bold">Filter Tanggal: </span>
                                        <div id="created_at" title="Tanggal" class="pull-right" style="background: #fff; cursor: pointer; padding: 3px 10px; border: 1px solid #ccc; border-radius: 4px; width: auto;">
                                            <i class="fa-regular fa-calendar-days"></i>&nbsp;
                                            <span></span> <b class="caret"></b>
                                        </div>
                                    </div>
                                    
                                    <div style="display: none;" id="buttonDownload">
                                        <a target="_blank" href="javascript:void(0);" class="btn btn-primary btn-sm ml-1" id="exportpdf" title="Export File">Export PDF <i class="fa-regular fa-file-pdf ml-1"></i></a>
                                        <a target="_blank" href="javascript:void(0);" class="btn btn-info btn-sm ml-1" id="exportExcel" title="Download File Excel">Export Excel <i class="fa-solid fa-file-excel ml-1"></i></a>
                                    </div>
                                
                                </div>
                                <div class="table-responsive">
                                    <table id="ordersReportReturnTable" class="table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Tanggal</th>
                                                <th style="padding: 10px 10px;" class="text-capitalize">Invoice</th>
                                                <th style="padding: 10px 10px;">Pelanggan</th>
                                                <th style="padding: 10px 10px; width: 15%;">Produk</th>
                                                <th style="padding: 10px 10px;">Alasan Pengembalian</th>
                                                <th style="padding: 10px 10px;">Total</th>
                                            </tr>
                                        </thead>
                                    </table>
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
    <a href="{{ route('report.newReturnDatatables') }}" id="reportsReturnGetData"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            let start = moment().startOf('month')
            let end = moment().endOf('month')

            function updateExportLink(start, end) {
                $('#exportpdf').data('href', '/seller/reports/reportreturn/' + start.format('YYYY-MM-DD') + '+' + end.format('YYYY-MM-DD'));
                $('#exportExcel').data('href', '{{ route("report.newReturnExcel", ":daterange") }}'
    .replace(':daterange', start.format('YYYY-MM-DD') + '+' + end.format('YYYY-MM-DD')));
            }

            updateExportLink(start, end);

            function cb(start, end) {
                $('#created_at span').html(start.locale(`id`).format('dddd, DD MMMM YYYY') + ' - ' + end.locale("id").format('dddd, DD MMMM YYYY'));
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
                    searchPlaceholder: 'Cari Produk...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada laporan'
                },
                initComplete: function() {
                    var $searchInput = $('#ordersReportReturnTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Pesanan...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#ordersReportReturnTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#ordersReportReturnTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#reportsReturnGetData').attr('href');
            var table = $('#ordersReportReturnTable').DataTable({
                ajax: {
                    url: url,
                    data: function(d) {
                        d.start_date = $('#created_at').data('daterangepicker').startDate.format('dddd, D MMMM YYYY');
                        d.end_date = $('#created_at').data('daterangepicker').endDate.format('dddd, D MMMM YYYY');
                    },

                    beforeSend: function() {
                        $('#loaderArea').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
                            overlayCSS: {
                                backgroundColor: '#fff',
                                opacity: 0.8,
                                cursor: 'wait'
                            },
                            css: {
                                border: 0,
                                padding: 0,
                                backgroundColor: 'none',
                                '-webkit-border-radius': '10px', 
                                '-moz-border-radius': '10px', 
                            }
                        }); // Show loader before request
                    },
                    complete: function() {
                        $('#loaderArea').unblock(); // Hide loader after request complete
                    }
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = table.page.info();
                    var value = index + 1 + info.start;
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false, className: 'text-center align-middle'},
                    {data: 'formattedReturnDate', name: 'formattedReturnDate', className: 'align-middle'}, 
                    {data: 'invoice', name: 'invoice', className: 'text-uppercase align-middle font-weight-bold'},
                    {data: 'customer_name', name: 'customer_name', className: 'align-middle'},
                    {data: 'product', name: 'product', className: 'align-middle'},
                    {data: 'reason', name: 'reason', orderable: false, searchable: false, className: 'align-middle'},
                    {data: 'totalRefund', name: 'totalRefund', className: 'align-middle'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            table.on('draw.dt', function() {
                var PageInfo = table.page.info();
                table.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start + '.';
                });
            });

            $('#created_at').on('apply.daterangepicker', function(ev, picker) {
                // Format the start and end dates using moment.locale
                let startDate = moment(picker.startDate).locale(`id`).format('dddd, D MMMM YYYY');
                let endDate = moment(picker.endDate).locale(`id`).format('dddd, D MMMM YYYY');
                
                table.ajax.url('/seller/reports/return/datatables?date=' + startDate + ' - ' + endDate).load();
                
                // Update the span text inside the date range picker
                $('#created_at span').html(startDate + ' - ' + endDate);

                // change style none
                $('#buttonDownload').css('display', 'block');

            });

            $('#exportpdf').on('click', function(e) {
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
                                $('#loaderArea').block({ 
                                    message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
                                    overlayCSS: {
                                        backgroundColor: '#fff',
                                        opacity: 0.8,
                                        cursor: 'wait'
                                    },
                                    css: {
                                        border: 0,
                                        padding: 0,
                                        backgroundColor: 'none',
                                        border: 'none',
                                        '-webkit-border-radius': '10px', 
                                        '-moz-border-radius': '10px', 
                                        color: '#000' 
                                    }
                                });
                            },
                            complete: function() {
                                $('#loaderArea').unblock();
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

            $('#exportExcel').on('click', function(e) {
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
                                $('#loaderArea').block({ 
                                    message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
                                    overlayCSS: {
                                        backgroundColor: '#fff',
                                        opacity: 0.8,
                                        cursor: 'wait'
                                    },
                                    css: {
                                        border: 0,
                                        padding: 0,
                                        backgroundColor: 'none',
                                        border: 'none',
                                        '-webkit-border-radius': '10px', 
                                        '-moz-border-radius': '10px', 
                                        color: '#000' 
                                    }
                                });
                            },
                            complete: function() {
                                $('#loaderArea').unblock();
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
            
        })
    </script>
@endsection