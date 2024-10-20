@extends('layouts.admin')

@section('title')
    <title>Daftar Produk</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Produk</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Produk</li>
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
                                <h4 class="card-title">Daftar Produk</h4>
                                <div class="float-right">
                                    {{-- <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#bulkUploadModal">Mass Upload <span class="fa-regular fa-file ml-1"></span></button>
                                    <a href="{{ route('product.newCreate') }}" class="btn btn-sm btn-primary">Tambah Produk <span class="fa-solid fa-plus ml-1"></span></a> --}}
                                </div>
                            </div>
                            <div class="card-body loader-area">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                <div class="table-responsive">
                                    <table id="productsTable" style="width: 100%;" class="table table-body">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Gambar</th>
                                                <th style="padding: 10px 10px;">Nama</th>
                                                <th style="padding: 10px 10px; width: 40%;">Deskripsi</th>
                                                <th style="padding: 10px 10px; width: 10%;">Harga</th>
                                                <th style="padding: 10px 10px;">Status</th>
                                                <th style="padding: 10px 10px;" class="text-center">Opsi</th>
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

    <!-- Modal Detail Product -->
    <div class="modal fade" id="productDetailModal" tabindex="-1" role="dialog" aria-labelledby="productDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 80%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productDetailModalLabel">Detail Produk</h5>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody id="productDetailsContent">
                            <!-- Product details will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Upload Modal -->
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" role="dialog" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 80%;" role="document">
            <form id="bulkUploadForm" action="{{ route('product.newSaveBulk') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkUploadModalLabel">Mass Upload Produk</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_id">Kategori</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($category as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-danger">{{ $errors->first('category_id') }}</p>
                        </div>
                        <div class="form-group">
                            <label for="file">File Excel</label>
                            <input type="file" name="file" id="fileInput" class="form-control" required>
                            <span>*NB: Format File .xlsx</span>
                            <p class="text-danger">{{ $errors->first('file') }}</p>
                        </div>
                        <div class="form-group">
                            <label>Preview</label>
                            <div id="preview" class="table-responsive"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- IMPORTANT LINK -->
    <a href="{{ route('product.getDatatables') }}" id="productsGetDatatables"></a>
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
                searchPlaceholder: 'Cari Produk...',
                lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                emptyTable: 'Tidak ada laporan'
            },
            initComplete: function() {
                var $searchInput = $('#productsTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Produk...');
                $searchInput.parent().addClass('d-flex align-items-center');

                var $lengthMenu = $('#productsTable_length select').addClass('form-control form-control-sm');

                $lengthMenu.parent().addClass('d-flex align-items-center');
                
                $('#productsTable_length').addClass('d-flex align-items-center');
            }
        });

        var url = $('#productsGetDatatables').attr('href');
        var table = $('#productsTable').DataTable({
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
                var value = index + 1 + info.start;
                $('td', row).eq(0).html(value);
            },
            columns: [
                {data: null, sortable: false, orderable: false, searchable: false},
                {data: 'image', name: 'image', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description', width: '40%' },
                {data: 'price', name: 'price', render: function(data, type, row) {
                        // format angka
                        var formattedPrice = 'Rp ' + numeral(data).format('0,0');
                        return formattedPrice.replace(',', '.');
                    }
                },
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', width: '10%', orderable: false, searchable: false, className: 'text-center' }
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            error: function(xhr, errorType, exception) {
                console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
            }
        });

        $('#productsTable').on('click', '.detail-product', function() {
            var productId = $(this).data('product-id');
            var showUrl = '{{ route("product.detail", ":id") }}'.replace(':id', productId);

            $.ajax({
                url: showUrl,
                type: 'GET',
                beforeSend: function() {
                    $('#productDetailModal').modal('show');
                    $('#productDetailsContent').block({ 
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
                    $('#productDetailsContent').unblock(); // Hide loader after request complete
                },
                success: function(response) {
                    // Create table rows from the response
                    var productDetailsHtml = `
                        <tr>
                            <th style="width: 15%;">Nama Penjual</th>
                            <td style="width: 50%;">${response.seller}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Nama Produk</th>
                            <td style="width: 50%;">${response.name}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Kategori Produk</th>
                            <td style="width: 50%;">${response.category}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Deskripsi Produk</th>
                            <td style="width: 50%;">${response.description}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Harga Produk</th>
                            <td style="width: 50%;">${response.price}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Berat Produk</th>
                            <td style="width: 50%;">${response.weight}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Stok Produk</th>
                            <td style="width: 50%;">${response.stock}</td>
                        </tr>
                        ${response.storage_instructions !== '-' && response.storage_instructions !== null ? `
                            <tr>
                                <th style="width: 15%;">Petunjuk Penyimpanan</th>
                                <td style="width: 50%;">${response.storage_instructions}</td>
                            </tr>
                        ` : ''}
                        ${response.storage_period !== '-' && response.storage_period !== null ? `
                            <tr>
                                <th style="width: 15%;">Masa Penyimpanan</th>
                                <td style="width: 50%;">${response.storage_period}</td>
                            </tr>
                        ` : ''}
                        ${response.units !== '-' && response.units !== null ? `
                            <tr>
                                <th style="width: 15%;">Unit</th>
                                <td style="width: 50%;">${response.units}</td>
                            </tr>
                        ` : ''}
                        ${response.packaging !== '-' && response.packaging !== null ? `
                            <tr>
                                <th style="width: 15%;">Kemasan</th>
                                <td style="width: 50%;">${response.packaging}</td>
                            </tr>
                        ` : ''}
                        ${response.serving_suggestions !== '-' && response.serving_suggestions !== null ? `
                            <tr>
                                <th style="width: 10%;">Saran Penyajian</th>
                                <td style="width: 50%;">${response.serving_suggestions}</td>
                            </tr>
                        ` : ''}
                        ${response.status !== '-' && response.status !== null ? `
                            <tr>
                                <th style="width: 15%;">Status Produk</th>
                                <td style="width: 50%;">${response.status}</td>
                            </tr>
                        ` : ''}
                        ${response.type !== '-' && response.type !== null ? `
                            <tr>
                                <th style="width: 10%;">Tipe</th>
                                <td style="width: 50%;">
                                    <span class="badge badge-danger">${response.type}</span>
                                </td>
                            </tr>
                        ` : ''}
                        ${response.promo_date !== ' - ' && response.promo_date !== null && response.promo_date !== '' ? `
                            <tr>
                                <th style="width: 10%;">Waktu Promo</th>
                                <td style="width: 50%;">${response.promo_date}</td>
                            </tr>
                        ` : ''}
                        ${response.promo_price !== '-' && response.promo_price !== null ? `
                            <tr>
                                <th style="width: 10%;">Harga Promo</th>
                                <td style="width: 50%;">${response.promo_price}</td>
                            </tr>
                        ` : ''}
                        <tr>
                            <th style="width: 10%;">Gambar Produk</th>
                            <td style="width: 50%;">
                                <div style="width: 100px; height: 100px; display: flex;">
                                    <img src="${response.image}" alt="${response.name}" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                            </td>
                        </tr>
                    `;

                    // Insert the HTML into the table body
                    $('#productDetailsContent').html(productDetailsHtml);
                    // Show the modal
                    $('#productDetailModal').modal('show');
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal memuat detail produk. Silahkan coba lagi nanti.',
                        icon: 'error'
                    });
                }
            });
        });

        // hapus produk
        $('#productsTable').on('click', '.delete-product', function(e) {
            e.preventDefault();

            var productId = $(this).data('product-id');
            var deleteUrl = '{{ route("product.newDestroy", ":id") }}'.replace(':id', productId);

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus produk ini?',
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
                                    text: response.message,
                                    icon: 'success'
                                }).then(function() {
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            var errorMessage = xhr.status + ': ' + xhr.statusText;
                            Swal.fire({
                                title: 'Error',
                                text: 'Terjadi Kesalahan ' + errorMessage,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

        $('#bulkUploadForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#bulkUploadModal').modal('hide');
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
                    $('#submitBtn').html('Upload');
                    $('#bulkUploadModal').modal('hide');
                    $.unblockUI();

                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success'
                        }).then(function() {
                            window.location.href = "{{ route('product.index') }}";
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi Kesalahan ' + errorMessage,
                        icon: 'error'
                    });
                    $('#submitBtn').attr('disabled', false).html('Upload');
                }
            });
        });

        document.getElementById('fileInput').addEventListener('change', function(e) {
            var file = e.target.files[0];
            var fileType = file.type;

            // Check if the file is an xlsx file
            if (fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var data = new Uint8Array(e.target.result);
                    var workbook = XLSX.read(data, {type: 'array'});
                    var firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    var json = XLSX.utils.sheet_to_json(firstSheet, {header: 1});
                    var html = '<table class="table table-bordered">';
                    for (var i = 0; i < json.length; i++) {
                        html += '<tr>';
                        for (var j = 0; j < json[i].length; j++) {
                            if (i == 0) {
                                html += '<th>' + json[i][j] + '</th>';
                            } else {
                                html += '<td>' + json[i][j] + '</td>';
                            }
                        }
                        html += '</tr>';
                    }
                    html += '</table>';
                    document.getElementById('preview').innerHTML = html;
                };
                reader.readAsArrayBuffer(file);
            } else {
                var previewElement = document.getElementById('preview');
                previewElement.innerHTML = '<div class="alert alert-danger">Format file tidak mendukung.</div>';

                setTimeout(function() {
                    previewElement.innerHTML = '';
                }, 2000);
            }
        });

    });

</script>
@endsection