@extends('layouts.seller')

@section('title')
    <title>Daftar Pesanan Dibatalkan</title>
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
                        <h1 class="m-0 text-dark">Pesanan Dibatalkan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('seller.dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pesanan Dibatalkan</li>
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
                            <div class="card-header">
                                <h4 class="card-title">Daftar Pesanan Dibatalkan</h4>
                            </div>
                            <div class="card-body loader-area">
                                <div class="table-responsive">
                                    <table id="orderCancelTable" style="width: 100%;" class="table">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Tanggal</th>
                                                <th class="text-capitalize" style="padding: 10px 10px; width: 18%;"><span class="float-left">Invoice</span></th>
                                                <th style="padding: 10px 10px;" style="width: 15%;">Pelanggan</th>
                                                <th style="padding: 10px 10px;" style="width: 12%;">Total</th>
                                                <th style="padding: 10px 10px;" style="width: 15%;">Alasan</th>
                                                <th style="padding: 10px 10px;" style="width: 15%;">Produk</th>
                                                <th style="padding: 10px 10px;" style="width: 10%;">Opsi</th>
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

    <!-- Modal Detail -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailModal" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailModalLabel">Order Detail</h5>
                </div>
                <div class="modal-body loader-area-modal" id="orderDetailsContent">
                    <!-- Content will be dynamically inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- IMPORTANT LINK -->
    <a href="{{ route('orders.cancelGetDatatables') }}" id="orderCancelGetData"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        $(document).ready(function (){

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
                    var $searchInput = $('#orderCancelTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Pesanan...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    // Add Bootstrap form-control class to length menu dropdown
                    var $lengthMenu = $('#orderCancelTable_length select').addClass('form-control form-control-sm');

                    // Add display flex and align-items-center to the parent of the length menu
                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    // Ensure the 'Tampil' label is vertically aligned
                    $('#orderCancelTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#orderCancelGetData').attr('href');
            var table = $('#orderCancelTable').DataTable({
                ajax: {
                    url: url,
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
                    complete: function() {
                        $('.loader-area').unblock();
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
                    {data: 'formattedDate', name: 'formattedDate', className: 'text-capitalize align-middle'},
                    {data: 'invoice', name: 'invoice', className: 'align-middle font-weight-bold'},
                    {data: 'customer_name', name: 'invoice', className: 'align-middle'},
                    {data: 'total', name: 'total', className: 'align-middle align-middle'},
                    {data: 'reason', name: 'reason', className: 'align-middle'},
                    {data: 'details', name: 'details', className: 'align-middle'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'align-middle'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // delete order
            $('#orderCancelTable').on('click', '.delete-order', function(e) {
                event.preventDefault(); 

                var ordersId = $(this).data('order-id');
                var invoice = $(this).data('invoice');
                var deleteUrl = '{{ route("orders.cancelDelete", ":id") }}'.replace(':id', ordersId);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus pesanan ' + invoice + ' ?',
                    icon: 'warning',
                    showCancelButton: true,
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
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
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.success,
                                        icon: 'success',
                                        timer: 1500, // Display for 2 seconds
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            table.ajax.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.error,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                $('.loader-area').unblock();
                                var response = JSON.parse(xhr.responseText);
                                if (response.error) {
                                    errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                }
                                Swal.fire({
                                    title: 'Error',
                                    text: errorMessage,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
