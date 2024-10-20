@extends('layouts.admin')

@section('title')
    <title>Kategori</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Kategori</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kategori</li>
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
                    <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY  -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <button class="btn btn-sm btn-primary float-right" data-toggle="modal" data-target="#addCategoryModal">Tambah Kategori <i class="fa fa-plus ml-1"></i></button>
                            </div>
                            <div class="card-body loader-area">
                                <div class="table-responsive">
                                    <table id="categoryTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Kategori</th>
                                                <th style="padding: 10px 10px;">Parent</th>
                                                <th style="padding: 10px 10px;">Tanggal</th>
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

    <!-- Modal Tambah Kategori -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="addCategoryForm" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Tambah Kategori</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama Kategori</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Nama Kategori">
                            <p class="text-danger" id="nameError"></p>
                        </div>
                        <div class="form-group">
                            <label for="parent_id">Kategori</label>
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="">Pilih Kategori</option>
                                @foreach ($parent as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-danger" id="parentIdError"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="form-group float-right">
                            <button class="btn btn-primary btn-md" type="submit">Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kategori -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editCategoryForm" action="{{ route('category.update', ':id') }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_name">Nama Kategori</label>
                                <input type="text" name="name" id="edit_name" class="form-control" placeholder="Masukkan Nama Kategori" required>
                                <p class="text-danger" id="editNameError"></p>
                            </div>
                            <div class="form-group">
                                <label for="edit_parent_id">Kategori</label>
                                <select name="parent_id" id="edit_parent_id" class="form-control">
                                    <option value="">None</option>
                                    @foreach ($parent as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-danger" id="editParentError"></p>
                            </div>
                            <input type="hidden" name="category_id" id="category_id">
                    </div>
                    <div class="modal-footer">
                        <div class="form-group">
                            <button class="btn btn-primary btn-md float-right" type="submit">Ubah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- IMPORTANT LINK -->
    <a href="{{ route('category.getDatatables') }}" id="categoryGetDatatables"></a>
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
                    searchPlaceholder: 'Cari Kategori...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada laporan'
                },
                initComplete: function() {
                    var $searchInput = $('#categoryTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Kategori...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#categoryTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#categoryTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#categoryGetDatatables').attr('href');
            var table = $('#categoryTable').DataTable({
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
                    {data: null, sortable: false, orderable: false, searchable: false, className: 'text-center'},
                    {data: 'name', name: 'name'},
                    {data: 'parent_name', name: 'parent_name'},
                    {data: 'formattedDate', name: 'formattedDate'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // tambah kategori
            $('#addCategoryForm').on('submit', function(event){
                event.preventDefault();

                $.ajax({
                    url: "{{ route('category.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('#addCategoryModal').modal('hide'); 
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
                    success: function(response){
                        // Unblock UI
                        $('.loader-area').unblock(); 

                        if(response.success) {
                            $('#addCategoryModal').modal('hide'); // Hide the modal
                            $('#addCategoryForm')[0].reset();
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
                        }   else {
                            Swal.fire({
                                title: 'Gagal',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(response){
                        // Unblock UI
                        $('.loader-area').unblock(); 

                        let errors = response.responseJSON.errors;
                        if(errors.name) {
                            $('#nameError').text(errors.name[0]);
                            $('#name').addClass('input-error');
                        } else {
                            $('#nameError').text('');
                            $('#name').removeClass('input-error');
                        }

                        if(errors.parent_id) {
                            $('#parentError').text(errors.parent_id[0]);
                            $('#parent_id').addClass('input-error');
                        } else {
                            $('#parentError').text('');
                            $('#parent_id').removeClass('input-error');
                        }

                        // Show error alert
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi Kesalahan, Silahkan Coba Lagi Nanti.',
                            icon: 'error',
                            timer: 2000, 
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                $('#addCategoryModal').modal('show'); 
                            }
                        });
                    }
                });
            });

            // hapus kategori
            $('#categoryTable').on('click', '.delete-category', function() {
                var categoryId = $(this).data('category-id');
                var categoryName = $(this).data('category-name');
                var deleteUrl = '{{ route("category.destroy", ":id") }}'.replace(':id', categoryId);
                
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus kategori ' + categoryName + '?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonColor: 'green',
                    cancelButtonText: 'Tidak',
                    confirmButtonText: 'Ya, Hapus',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}",
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
                            complete: function() {
                                $('.loader-area').unblock(); // Hide loader after request complete
                            },
                            success: function(response) {
                                if(response.success) {
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
                            error: function(xhr) {
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
                                        window.location.reload(true);
                                    }
                                });
                            }
                        });
                    }
                });
            });    

            // Edit category
            $('#categoryTable').on('click', '.edit-category', function() {
                var categoryId = $(this).data('category-id');
                var editUrl = '{{ route("category.edit", ":id") }}'.replace(':id', categoryId);

                $.ajax({
                    url: editUrl,
                    method: 'GET',
                    success: function(response) {
                        var category = response.category;
                        var parent = response.parent;

                        // Populate the modal fields with category data
                        $('#edit_name').val(category.name);
                        $('#edit_parent_id').val(category.parent_id);
                        $('#category_id').val(category.id); // Set category id for form submission

                        // Show the modal
                        $('#editCategoryModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax request error:', status, error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi Kesalahan, Silahkan Coba Lagi Nanti.',
                            icon: 'error'
                        });
                    }
                });
            });

            $('#editCategoryForm').on('submit', function(event) {
                event.preventDefault();
                var categoryId = $('#category_id').val();
                var updateUrl = '{{ route("category.update", ":id") }}'.replace(':id', categoryId);

                $.ajax({
                    url: updateUrl,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editCategoryModal').modal('hide');
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success',
                            timer: 2000, 
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                table.ajax.reload(); // Reload the page or update UI as needed
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax request error:', status, error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi Kesalahan, Silahkan Coba Lagi Nanti.',
                            icon: 'error'
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
    </style>
@endsection