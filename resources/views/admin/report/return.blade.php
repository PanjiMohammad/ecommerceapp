@extends('layouts.admin')

@section('title')
    <title>Laporan Pengembalian Pesanan</title>
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
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
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
                <div class="row">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST PRODUCT  -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Daftar Laporan Pengembalian Pesanan</h4>
                            </div>
                            <div class="card-body loader-area">
                                <div class="d-flex mb-3 justify-content-end">
                                    {{-- <div class="input-group flex-nowrap" style="width: 36%;">
                                        <span class="input-group-text" id="basic-addon1"><span class="fa-regular fa-calendar-days"></span></span>
                                        <input type="text" id="created_at" name="date" class="form-control" aria-describedby="basic-addon1">
                                    </div> --}}
                                    
                                    <div class="d-flex align-items-center">
                                        <span style="margin-top: 1px;" class="font-weight-bold mr-2">Filter Tanggal: </span>
                                        <div id="created_at" title="Tanggal" class="pull-right" style="background: #fff; cursor: pointer; padding: 3px 10px; border: 1px solid #ccc; width: auto; border-radius: 4px;">
                                            <i class="fa-regular fa-calendar-days"></i>&nbsp;
                                            <span></span> <b class="caret"></b>
                                        </div>
                                    </div>
                                    
                                    <div style="display: none" id="downloadButton">
                                        <a target="_blank" class="btn btn-primary btn-sm ml-1" id="exportpdf" title="Export File">Export PDF<i class="fa-regular fa-file-pdf ml-1"></i></a>
                                        <a target="_blank" class="btn btn-primary btn-sm ml-1" id="exportExcel" title="Download File Excel">Export Excel <i class="fa-solid fa-file-excel ml-1"></i></a>
                                    </div>

                                </div>
                                <div class="table-responsive">
                                    <table id="ordersReportReturnTable" class="table">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Tanggal</th>
                                                <th style="padding: 10px 10px;" class="text-capitalize">Invoice</th>
                                                <th style="padding: 10px 10px;">Pelanggan</th>
                                                <th style="padding: 10px 10px; width: 12%;">Produk</th>
                                                <th style="padding: 10px 10px;">Pengembalian</th>
                                                <th style="padding: 10px 10px;">Total</th>
                                                <th style="padding: 10px 10px;">Status</th>
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
    <a href="{{ route('report.orderReturnGetDatatables') }}" id="reportsOrderReturnGetData"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            let start = moment().startOf('month')
            let end = moment().endOf('month')

            function updateExportLink(start, end) {
                $('#exportpdf').attr('href', '/administrator/reports/reportreturn/' + start.format('YYYY-MM-DD') + '+' + end.format('YYYY-MM-DD'));
                $('#exportExcel').attr('href', '/administrator/reports/reportreturn/' + start.format('YYYY-MM-DD') + '+' + end.format('YYYY-MM-DD'));
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
                    searchPlaceholder: 'Cari Invoice...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada laporan'
                },
                initComplete: function() {
                    var $searchInput = $('#ordersReportReturnTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Invoice...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#ordersReportReturnTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#ordersReportReturnTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#reportsOrderReturnGetData').attr('href');
            var table = $('#ordersReportReturnTable').DataTable({
                ajax: {
                    url: url,
                    data: function(d) {
                        d.start_date = $('#created_at').data('daterangepicker').startDate.format('dddd, D MMMM YYYY');
                        d.end_date = $('#created_at').data('daterangepicker').endDate.format('dddd, D MMMM YYYY');
                    },

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
                        }); // Show loader before request
                    },
                    complete: function() {
                        $.unblockUI(); // Hide loader after request complete
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
                    {data: null, sortable: false, orderable: false, searchable: false, className: 'text-center'},
                    {data: 'formattedReturnDate', name: 'formattedReturnDate'},
                    {data: 'invoice', name: 'invoice', className: 'font-weight-bold text-uppercase align-middle'},
                    {data: 'customer_name', name: 'customer_name', className: 'align-middle'},
                    {data: 'product', name: 'product'},
                    {data: 'reason', name: 'reason', orderable: false, searchable: false},
                    {data: 'totalRefund', name: 'totalRefund', orderable: false, searchable: false},
                    {data: 'statusProduct', name: 'statusProduct', orderable: false, searchable: false},
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
                moment.locale('id');
                // Format the start and end dates using moment.locale
                let startDate = moment(picker.startDate).locale('id').format('dddd, D MMMM YYYY');
                let endDate = moment(picker.endDate).locale('id').format('dddd, D MMMM YYYY');
                
                table.ajax.url('/administrator/reports/return/getDatatables?date=' + startDate + ' - ' + endDate).load();
            
                $('#downloadButton').css({ "display" : "block" });
            });
        })
    </script>
@endsection