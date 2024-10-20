@extends('layouts.seller')

@section('title')
    <title>Pesanan Selesai</title>
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
                        <h1 class="m-0 text-dark">Pesanan Selesai</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('seller.dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pesanan Selesai</li>
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
                                <h4 class="card-title">Daftar Pesanan Selesai</h4>
                            </div>
                            <div class="card-body loader-area">
                                {{-- @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <form action="{{ route('orders.newIndex') }}" method="get">
                                    <div class="col-md-12">
                                        <div class="row float-right">
                                            <div class="form-group mr-1">
                                                <select name="status" class="form-control">
                                                    <option value="">Pilih Status</option>
                                                    <option value="0">Baru</option>
                                                    <option value="1">Konfirmasi</option>
                                                    <option value="2">Proses</option>
                                                    <option value="3">Dikirim</option>
                                                    <option value="4">Selesai</option>
                                                </select>
                                            </div>
                                            <div class="form-group mr-1">
                                                <input type="text" id="q" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                            </div>
                                            <div class="form-group mr-1">
                                                <button class="btn btn-secondary" type="submit">Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                </form> --}}

                                <div class="table-responsive">
                                    <table id="ordersTable" style="width: 100%;" class="table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th style="padding: 10px 10px;">Tanggal</th>
                                                <th class="text-capitalize" style="padding: 10px 10px; width: 18%;"><span class="float-left">Invoice</span></th>
                                                <th style="padding: 10px 10px;" style="width: 15%;">Pelanggan</th>
                                                <th style="padding: 10px 10px;" style="width: 10%;">Total</th>
                                                <th style="padding: 10px 10px;" style="width: 15%;">Produk</th>
                                                <th style="padding: 10px 10px;" style="width: 17%;">Status</th>
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
    
    <!-- IMPORTANT LINK -->
    <a href="{{ route('orders.finishDatatables') }}" id="ordersGetData"></a>
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
                    var $searchInput = $('#ordersTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Pesanan...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    // Add Bootstrap form-control class to length menu dropdown
                    var $lengthMenu = $('#ordersTable_length select').addClass('form-control form-control-sm');

                    // Add display flex and align-items-center to the parent of the length menu
                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    // Ensure the 'Tampil' label is vertically aligned
                    $('#ordersTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#ordersGetData').attr('href');
            var table = $('#ordersTable').DataTable({
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
                    {data: 'formattedDate', name: 'formattedDate', className: 'align-middle'}, 
                    {data: 'invoice', name: 'invoice', className: 'font-weight-bold text-uppercase align-middle', width: '18%'},
                    {data: 'customer_name', name: 'customer_name', className: 'align-middle', width: '15%'},
                    {data: 'totalProduct', name: 'totalProduct', className: 'align-middle', width: '10%'},
                    {data: 'details', name: 'details', className: 'align-middle', width: '15%'},
                    {data: 'statusProduct', name: 'statusProduct', orderable: false, width: '17%', className: 'align-middle'}, 
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'align-middle', width: '10%'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            $('#ordersTable').on('click', '.view-order', function(e) {
                var invoice = $(this).data('invoice');
                const index = $(this).data('index');
                var url = "{{ route('orders.newView', ':invoice') }}".replace(':invoice', invoice);

                // Send Ajax request to fetch the order details and redirect to the detail page
                $.ajax({
                    url: url,
                    method: 'GET',
                    beforeSend: function() {
                        $('.view-order[data-index="' + index + '"]').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i>', 
                            overlayCSS: {
                                backgroundColor: '#fff',
                                opacity: 0.8,
                                cursor: 'wait',
                            },
                            css: {
                                border: 0,
                                padding: 0,
                                backgroundColor: 'none',
                            }
                        });
                    },
                    complete: function() {
                        $('.view-order[data-index="' + index + '"]').unblock();
                    },
                    success: function(response) {
                        // Hide the loader
                        $('#loader').hide();

                        // Redirect to the order detail page with the fetched content
                        window.location.href = url;
                    },
                    error: function(xhr) {
                        // Handle errors (optional)
                        $('#loader').hide();
                        alert('Error fetching order details.');
                    }
                });
            });

            $('#ordersTable').on('click', '.delete-order', function(e) {
                event.preventDefault(); 

                var ordersId = $(this).data('order-id');
                var deleteUrl = '{{ route("orders.newDestroy", ":id") }}'.replace(':id', ordersId);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus pesanan ini?',
                    icon: 'warning',
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
