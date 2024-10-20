@extends('layouts.admin')

@section('title')
    <title>Penarikan Dana</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Penarikan Dana</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Penarikan Dana</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <section class="content">
            <div class="container">
            
                @if (session('success'))
                    <input type="hidden" id="success-message" value="{{ session('success') }}">
                @endif

                @if (session('error'))
                    <input type="hidden" id="error-message" value="{{ session('error') }}">
                @endif
            
                <div class="card">
                    <div class="card-body loader-area">
                        <div class="table-responsive">
                            <table class="table table-stripped" id="withdrawTable">
                                <thead>
                                    <tr>
                                        <th style="padding: 10px 10px;">#</th>
                                        <th style="padding: 10px 10px;">Nama Penjual</th>
                                        <th style="padding: 10px 10px;">Jumlah</th>
                                        <th style="padding: 10px 10px;">Bank</th>
                                        <th style="padding: 10px 10px;">Nomor Akun</th>
                                        <th style="padding: 10px 10px;">Status</th>
                                        <th style="padding: 10px 10px;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- IMPORTANT LINK -->
    <a href="{{ route('withdraw.getDatatables') }}" id="withdrawalsGetDatatables"></a>
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
                    searchPlaceholder: 'Cari Kategori...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada data',
                },
                initComplete: function() {
                    var $searchInput = $('#withdrawTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Nama...');
                    $searchInput.parent().addClass('d-flex align-items-center float-right');

                    var $lengthMenu = $('#withdrawTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#withdrawTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#withdrawalsGetDatatables').attr('href');
            var table = $('#withdrawTable').DataTable({
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = table.page.info();
                    var value = index + 1 + info.start + '.';
                    $('td', row).eq(0).html(value);
                },
                ajax: {
                    url: url,
                    type: 'GET', // Use GET request for retrieving data
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
                    error: function(xhr, errorType, exception) {
                        console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                    }
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false},
                    { data: 'sellerName', name: 'seller.name' },
                    { data: 'amount', name: 'amount', render: $.fn.dataTable.render.number('.', ',', 0, 'Rp ') },
                    { data: 'bankName', name: 'bankName' },
                    { data: 'accountNumber', name: 'accountNumber' },
                    { data: 'status', name: 'status', className: 'text-capitalize' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50]
            });
            
            // Handle form submission via Ajax
            $('#withdrawTable').on('submit', '.withdraw-action-form', function(e) {
                e.preventDefault(); // Prevent the default form submission

                var $form = $(this);
                var id = $form.data('id');
                var status = $form.find('button').text().trim();
                var formData = $form.serialize();
                var url = $form.attr('action');

                Swal.fire({
                    title: 'Apa anda yakin ?',
                    text: "Apakah Anda ingin mengubah status menjadi " + status + "?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Tolak',
                    confirmButtonText: 'Setuju',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            beforeSend: function () {
                                $('.loader-area').block({
                                    message: '<i class="icon-spinner4 spinner"></i>',
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
                            success: function(response) {
                                if(response.success == true) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        timer: 1500,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            table.ajax.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        timer: 2000,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            location.reload(true);
                                        }
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred: ' + xhr.status + ' ' + xhr.statusText,
                                    timer: 2000,
                                    showCancelButton: false,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection