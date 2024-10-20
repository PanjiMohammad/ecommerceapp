@extends('layouts.seller')

@section('title')
    <title>Produk</title>
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('seller.dashboard') }}">Dashboard</a>
                            </li>
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
                                <div class="float-right">
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#bulkUploadModal">Mass Upload <span class="fa-regular fa-file ml-1"></span></button>
                                    <a href="{{ route('product.newCreate') }}" class="btn btn-primary btn-sm">Tambah Produk <span class="fa-solid fa-plus ml-1"></span></a>
                                </div>
                            </div>
                            <div class="card-body" id="loaderArea">
                                {{-- <!-- BUAT FORM UNTUK PENCARIAN, METHODNYA ADALAH GET -->
                                <form action="{{ route('product.newIndex') }}" method="get">
                                    <div class="col-md-12">
                                        <div class="row float-right">
                                            <div class="form-group mr-1">
                                                <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                            </div>
                                            <div class="form-group mr-1">
                                                <button class="btn btn-secondary" type="submit">Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                </form> --}}

                                <div class="table-responsive">
                                    <table id="productTable" style="width: 100%;" class="table table-body">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Gambar</th>
                                                <th style="padding: 10px 10px;">Nama</th>
                                                <th style="padding: 10px 10px; width: 30%;">Deskripsi</th>
                                                <th style="padding: 10px 10px;">Harga</th>
                                                {{-- <th style="padding: 10px 10px;">Stok</th> --}}
                                                <th style="padding: 10px 10px;">Status</th>
                                                <th style="padding: 10px 10px;">Opsi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <!-- FUNGSI INI AKAN SECARA OTOMATIS MEN-GENERATE TOMBOL PAGINATION  -->
                                {{-- {!! $product->links() !!} --}}
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
        <div class="modal-dialog" role="document" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productDetailModalLabel">Produk Detail</h5>
                </div>
                <div class="modal-body modal-loader-area">
                    <table class="table table-bordered">
                        <tbody id="productDetailsContent">
                            <!-- Product details will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
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
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">Pilih Kategori</option>
                                @foreach ($category as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-danger" id="category_id_error"></p>
                        </div>
                        <div class="form-group">
                            <label for="file">File Excel</label>
                            <input type="file" name="file" id="fileInput" class="form-control">
                            <small>*NB: Format File .xlsx</small>
                            <p class="text-danger" id="file_error"></p>
                        </div>
                        <div class="form-group">
                            <label>Preview</label>
                            <div id="preview" class="table-responsive"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- IMPORTANT LINK -->
    <a href="{{ route('product.datatables') }}" id="product_get_data"></a>
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
                var $searchInput = $('#productTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Produk...');
                $searchInput.parent().addClass('d-flex align-items-center');

                var $lengthMenu = $('#productTable_length select').addClass('form-control form-control-sm');

                $lengthMenu.parent().addClass('d-flex align-items-center');
                
                $('#productTable_length').addClass('d-flex align-items-center');
            }
        });

        var url = $('#product_get_data').attr('href');
        var table = $('#productTable').DataTable({
            ajax: {
                url: url,
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
                            backgroundColor: 'none'
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
                {data: null, sortable: false, orderable: false, searchable: false},
                {data: 'image', name: 'image', orderable: false, searchable: false},
                {data: 'productName', name: 'productName'},
                {data: 'description', name: 'description', width: '30%'},
                {data: 'price', name: 'price', render: function(data, type, row) {
                        // format angka
                        var formattedPrice = 'Rp ' + numeral(data).format('0,0');
                        return formattedPrice.replace(',', '.');
                    }
                },
                // {data: 'stock', name: 'stock', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            error: function(xhr, errorType, exception) {
                console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
            }
        });

        $('#productTable').on('click', '.detail-product', function() {
            var productId = $(this).data('product-id');
            const index = $(this).data('index');
            var showUrl = '{{ route("product.newShow", ":id") }}'.replace(':id', productId);

            $.ajax({
                url: showUrl,
                type: 'GET',
                beforeSend: function() {
                    $('.detail-product[data-index="' + index + '"]').block({ 
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
                    $('.detail-product[data-index="' + index + '"]').unblock();
                },
                success: function(response) {
                    // Create table rows from the response
                    var productDetailsHtml = `
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
                                <th style="width: 15%;">Packaging</th>
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
                        text: 'Could not load product details. Please try again later.',
                        icon: 'error'
                    });
                }
            });
        });

        // hapus produk
        $('#productTable').on('click', '.delete-product', function(e) {
            e.preventDefault();

            var productId = $(this).data('product-id');
            const index = $(this).data('index');
            var deleteUrl = '{{ route("product.newDestroy", ":id") }}'.replace(':id', productId);

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus produk ini?',
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
                            $('.delete-product[data-index="' + index + '"]').block({ 
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
                            $('.delete-product[data-index="' + index + '"]').unblock();
                        },
                        success: function(response) {
                            if (response.success == true) {
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000, // Display for 2 seconds
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        table.ajax.reload();
                                    }
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

        // reset preview
        $('#bulkUploadModal').on('hidden.bs.modal', function () {
            $('#preview').empty();  // Clear the preview content
            $('#fileInput').val(''); // Reset the file input
        });

        $('#bulkUploadForm').on('submit', function(e) {
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
                complete: function() {
                    $.unblockUI();
                },
                success: function(response) {
                    $('#submitBtn').html('Upload');
                    $('#bulkUploadModal').modal('hide');

                    if(response.success == true){
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success',
                            timer: 2000, // Display for 2 seconds
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
                            icon: 'error',
                            timer: 2000, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                        });
                    }

                    // Clear the preview content
                    $('#preview').empty();  
                    $('#fileInput').val('');
                },
                error: function(xhr, status, error) {
                    let errors = xhr.responseJSON.errors;
                    let input = xhr.responseJSON.input;

                    // Clear previous errors
                    $('.text-danger').text('');

                    var response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                    }
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                    });
                    $('#submitBtn').attr('disabled', false).html('Upload');

                    $('#preview').empty();  // Clear the preview content
                    $('#fileInput').val('');
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
                    window.location.reload(true);
                }, 2000);
            }
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