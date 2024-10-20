@extends('layouts.admin')

@section('title')
    <title>Data Konsumen</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Konsumen</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Konsumen</li>
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
                                <a href="{{ route('consumen.create') }}" class="btn btn-primary btn-sm float-right">Tambah Konsumen<i class="fas fa-plus ml-1"></i></a>
                            </div>
                            <div class="card-body loader-area">
                                <div class="table-responsive">
                                    <table id="customerTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Nama</th>
                                                <th style="padding: 10px 10px;">Email</th>
                                                <th style="padding: 10px 10px;">Nomor Telepon</th>
                                                <th style="padding: 10px 10px;">Alamat</th>
                                                <!-- <th>Created At</th> -->
                                                <th style="padding: 10px 10px;">Status</th>
                                                <th style="padding: 10px 10px;">Opsi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <!-- FUNGSI INI AKAN SECARA OTOMATIS MEN-GENERATE TOMBOL PAGINATION  -->
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
    <a href="{{ route('consumen.getDatatables') }}" id="consumenGetDatatables"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            
            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Konsumen...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada laporan'
                },
                initComplete: function() {
                    var $searchInput = $('#customerTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Konsumen...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#customerTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#customerTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#consumenGetDatatables').attr('href');
            var table = $('#customerTable').DataTable({
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
                        }); // Show loader before request
                    },
                    complete: function() {
                        $('.loader-area').unblock(); // Hide loader after request complete
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
                    {data: null, sortable: false, orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'address', name: 'address'},
                    {data: 'status', name: 'status', className: 'text-center', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // delete customer
            $('#customerTable').on('click', '.delete-customer', function(e) {
                e.preventDefault();

                var consumenId = $(this).data('customer-id');
                var deleteUrl = '{{ route("consumen.destroy", ":id") }}'.replace(':id', consumenId);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus konsumen ini?',
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
                                }); // Show loader before request
                            },
                            success: function(response) {
                                $('.loader-area').unblock(); // Hide loader after request complete
                                if (response.success == true) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000, 
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            table.ajax.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                $('.loader-area').unblock(); // Hide loader after request complete
                                var response = JSON.parse(xhr.responseText);
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
                                        table.ajax.reload();
                                    }
                                });;
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection

@section('css')
    
@endsection