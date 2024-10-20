@extends('layouts.admin')

@section('title')
    <title>Daftar Pesanan</title>
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
                        <h1 class="m-0 text-dark">Pesanan</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pesanan</li>
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
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST PRODUCT  -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Daftar Pesanan</h4>
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
                                    <table id="ordersTable" style="width: 100%;" class="table table-body">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th style="padding: 10px 10px;">Tanggal</th>
                                                <th class="text-capitalize" style="padding: 10px 10px;"><span class="float-left">Invoice</span></th>
                                                <th style="padding: 10px 10px;" class="text-capitalize">Nama Pelanggan</th>
                                                <th style="padding: 10px 10px;">Total</th>
                                                <th style="padding: 10px 10px;">Produk</th>
                                                <th style="padding: 10px 10px;">Status</th>
                                                <th style="padding: 10px 10px;">Opsi</th>
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
    <a href="{{ route('orders.getDatatables') }}" id="ordersGetDataTables"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        $(document).ready(function(){

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
                    var $searchInput = $('#ordersTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Invoice...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#ordersTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#ordersTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#ordersGetDataTables').attr('href');
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
                    {data: null, sortable: false, orderable: false, searchable: false , className: 'text-center align-middle'},
                    {data: 'formattedDate', name: 'formattedDate', className: 'align-middle'},
                    {data: 'invoice', name: 'invoice', className: 'text-left text-uppercase font-weight-bold align-middle'},
                    {data: 'customer_name', name: 'customer_name', className: 'align-middle'},
                    {data: 'totalProduct', name: 'totalProduct', className: 'align-middle'}, 
                    {data: 'details', name: 'details', className: 'align-middle'}, 
                    {data: 'statusProduct', name: 'statusProduct', className: 'align-middle', orderable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'align-middle'},
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });
            
            $('#ordersTable').on('click', '.detail-order', function(e) {
                e.preventDefault();
                
                const orderDetailsModal = $('#orderDetailModal');
                const orderDetailsContent = $('#orderDetailsContent');
                
                const orderInvoice = $(this).data('order-invoice');
                const index = $(this).data('index');
                const showUrl = '{{ route("orders.detailView", ":invoice") }}'.replace(':invoice', orderInvoice);

                $.ajax({
                    url: showUrl,
                    type: 'GET',
                    beforeSend: function() {
                        $('.detail-order[data-index="' + index + '"]').block({ 
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
                        $('.detail-order[data-index="' + index + '"]').unblock();
                    },
                    success: function(response) {
                        orderDetailsContent.empty();
                        
                        if (response.orders) {
                            // Generate HTML content for order and customer details
                            let orderHtml = `
                                <div class="card">
                                    <div class="card-body">
                                        <p style="margin-bottom: 7px;"><span class="font-weight-bold">Invoice :</span> ${response.invoice}</p>
                                        <p style="margin-bottom: 7px;"><span class="font-weight-bold">Nama Pelanggan :</span> ${response.customer_name} (${response.customer_phone})</p>
                                        <p style="margin-bottom: 7px;"><strong>Email Pelanggan :</strong> ${response.customer_email}</p>
                                        <p style="margin-bottom: 7px;"><strong>Alamat Pelanggan :</strong> ${response.customer_address}, Kecamatan ${response.district}, Kota ${response.city}, ${response.province}, ${response.postal_code}.</p>
                                        <hr>
                                        <h5 class="mt-2 mb-2">Detail Produk:</h5>
                                        
                                        <!-- Start of the product table -->
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped" style="width: 100%">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Produk</th>
                                                        <th>Harga</th>
                                                        <th>Kuantiti</th>
                                                        <th>Berat</th>
                                                        <th>Kurir</th>
                                                        <th>Status</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                            `;

                            // Loop through products to dynamically add product details
                            response.products.forEach(product => {
                                orderHtml += `
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div style="width: 80px; height: 80px; display: block; border: 1px solid transparent; border-radius: 4px; background-color: white;">
                                                    <img src="${product.image}" alt="${product.name}" style="width: 100%; height: 100%; object-fit: contain;">
                                                </div>
                                                <div class="ml-2">
                                                    <span>${product.name}</span>
                                                </div>    
                                            </div>
                                        </td>
                                        <td class="align-middle">${product.price}</td>
                                        <td class="align-middle">${product.qty}</td>
                                        <td class="align-middle">${product.weight}</td>
                                        <td class="align-middle">${product.service}</td>
                                        <td class="align-middle">${product.status}</td>
                                        <td class="align-middle">${product.subtotal}</td>
                                    </tr>
                                `;
                            });

                            orderHtml += `
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Subtotal</td>
                                    <td class="text-center">:</td>
                                    <td class="font-weight-bold">${response.subtotal}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Ongkos Kirim</td>
                                    <td class="text-center">:</td>
                                    <td class="font-weight-bold">${response.shipping_cost}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Biaya Layanan</td>
                                    <td class="text-center">:</td>
                                    <td class="font-weight-bold">${response.service_cost}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Biaya Kemasan</td>
                                    <td class="text-center">:</td>
                                    <td class="font-weight-bold">${response.packaging_cost}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Total Belanja</td>
                                    <td class="text-center">:</td>
                                    <td class="font-weight-bold">${response.total}</td>
                                </tr>
                            `;

                            orderHtml += `
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- End of the product table -->
                                    </div>
                                </div>
                            `;

                            // Insert generated HTML into the modal body
                            orderDetailsContent.html(orderHtml);
                        } else {
                            orderDetailsContent.html('<p>No order details available.</p>');
                        }

                        // Show the modal
                        orderDetailsModal.modal('show');
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                        }
                        Swal.fire({
                            title: 'Error',
                            text: '',
                            icon: 'error',
                            timer: 2000,
                            willClose: () => {

                            }
                        });
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
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
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
                                });
                            },
                            success: function(response) {
                                $.unblockUI();
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.success,
                                        icon: 'success'
                                    }).then(function() {
                                        table.ajax.reload();
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
