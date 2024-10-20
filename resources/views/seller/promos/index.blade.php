@extends('layouts.seller')

@section('title')
    <title>Promo</title>
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
                        <h1 class="m-0 text-dark">Promo</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('seller.dashboard') }}">Beranda</a>
                            </li>
                            <li class="breadcrumb-item active">Promo</li>
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
                                <h4 class="card-title">Daftar Promo Produk</h4>
                                {{-- <div class="float-right">
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#bulkUploadModal">Mass Upload <span class="fa-regular fa-file ml-1"></span></button>
                                    <a href="{{ route('promoProduct.newCreate') }}" class="btn btn-primary btn-sm">Tambah Produk <span class="fa-solid fa-plus ml-1"></span></a>
                                </div> --}}
                            </div>
                            <div class="card-body loader-area">
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
                                    <table id="promoProductTable" class="table table-body">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">#</th>
                                                <th style="padding: 10px 10px;">Produk</th>
                                                <th style="padding: 10px 10px;" style="width: 30%;">Estimasi Waktu Promo</th>
                                                <th style="padding: 10px 10px;">Status</th>
                                                <th style="padding: 10px 10px;" style="width: 10%;">Opsi</th>
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
    <div class="modal fade" id="promoProductDetailModal" tabindex="-1" role="dialog" aria-labelledby="productDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 80%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promoProductDetailModalLabel">Produk Detail</h5>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody id="promoProductDetailsContent">
                            <!-- Product details will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Upload Modal -->
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" role="dialog" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 80%;" role="document">
            <form id="bulkUploadForm" action="{{ route('promoProduct.newSaveBulk') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkUploadModalLabel">Mass Upload Produk</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
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
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Estimati Waktu Promo</label>
                                    <div id="created_at" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                        <i class="fa-regular fa-calendar-days"></i>&nbsp;
                                        <span></span> <b class="caret"></b>
                                    </div>
                                </div>
                                <div class="input-estimate form-group">
                                    <input type="hidden" id="estimate-input-start" name="estimate_input_start" class="form-control" readonly>
                                    <input type="hidden" id="estimate-input-end" name="estimate_input_end" class="form-control" readonly>
                                </div>
                            </div>
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
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- IMPORTANT LINK -->
    <a href="{{ route('promoProduct.datatables') }}" id="promoProductsGetData"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
<script>
    $(document).ready(function(){

        let start = moment().startOf('month')
            let end = moment().endOf('month')

            // created_at
            function cb(start, end) {
                $('#created_at span').html(start.format('dddd, DD MMMM YYYY HH:mm') + ' - ' + end.format('dddd, DD MMMM YYYY HH:mm'));
            }

            $('#created_at').daterangepicker({
                startDate: start,
                endDate: end,
                locale: {
                    format: 'dddd, DD MMMM YYYY HH:mm',
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
            }, cb);

            cb(start, end);

            $('#created_at').on('apply.daterangepicker', function(ev, picker) {
                // Set the hidden input values to the selected date range
                $('#estimate-input-start').val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
                $('#estimate-input-end').val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
            });

        $.extend($.fn.dataTable.defaults, {
            autoWidth: false,
            autoLength: false,
            dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
            language: {
                search: '<span>Pencarian:</span> _INPUT_',
                searchPlaceholder: 'Cari Produk...',
                lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                emptyTable: 'Tidak ada data'
            },
            initComplete: function() {
                var $searchInput = $('#promoProductTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Produk...');
                $searchInput.parent().addClass('d-flex align-items-center');

                var $lengthMenu = $('#promoProductTable_length select').addClass('form-control form-control-sm');

                $lengthMenu.parent().addClass('d-flex align-items-center');
                
                $('#promoProductTable_length').addClass('d-flex align-items-center');
                
            }
        });

        var url = $('#promoProductsGetData').attr('href');
        var table = $('#promoProductTable').DataTable({
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
                {data: 'infoProduct', name: 'infoProduct', orderable: false, searchable: false},
                {data: 'rangeDate', name: 'rangeDate', width: '30%'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', width: '10%', orderable: false, searchable: false}
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            error: function(xhr, errorType, exception) {
                console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
            }
        });

        $('#promoProductTable').on('click', '.detail-promo', function() {
            var promoProductId = $(this).data('promo-id');
            const index = $(this).data('index');
            var showUrl = '{{ route("promoProduct.newShow", ":id") }}'.replace(':id', promoProductId);

            $.ajax({
                url: showUrl,
                type: 'GET',
                beforeSend: function() {
                    $('.detail-promo[data-index="' + index + '"]').block({ 
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
                    $('.detail-promo[data-index="' + index + '"]').unblock();
                },
                success: function(response) {
                    // Create table rows from the response
                    var promoProductDetailsHtml = `
                        <tr>
                            <th style="width: 10%;">Nama Produk</th>
                            <td style="width: 50%;">${response.name}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Kategori Produk</th>
                            <td style="width: 50%;">${response.category}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Deskripsi Produk</th>
                            <td style="width: 50%;">${response.description}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Harga Produk</th>
                            <td style="width: 50%;">${response.price}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Berat Produk</th>
                            <td style="width: 50%;">${response.weight}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Stok Produk</th>
                            <td style="width: 50%;">${response.stock}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Petunjuk Penyimpanan</th>
                            <td style="width: 50%;">${response.storage_instructions}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Masa Penyimpanan</th>
                            <td style="width: 50%;">${response.storage_period}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Unit</th>
                            <td style="width: 50%;">${response.units}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Packaging</th>
                            <td style="width: 50%;">${response.packaging}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Saran Penyajian</th>
                            <td style="width: 50%;">${response.serving_suggestions}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Status Produk</th>
                            <td style="width: 50%;">${response.status}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Tipe</th>
                            <td style="width: 50%;">
                                ${response.type === '-' ? `<span>${response.type}</span>` : `<span class="badge badge-danger">${response.type}</span>`}
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Waktu Promo</th>
                            <td style="width: 50%;">${response.promo_date}</td>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Harga Promo</th>
                            <td style="width: 50%;">${response.promo_price}</td>
                        </tr>
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
                    $('#promoProductDetailsContent').html(promoProductDetailsHtml);
                    // Show the modal
                    $('#promoProductDetailModal').modal('show');
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
        $('#promoProductTable').on('click', '.delete-promo', function(e) {
            e.preventDefault();

            var promoProductId = $(this).data('promo-id');
            var deleteUrl = '{{ route("promoProduct.newDestroy", ":id") }}'.replace(':id', promoProductId);

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
                    $('#bulkUploadForm')[0].reset();
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
                        }).then(function() {
                            table.ajax.reload();
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#submitBtn').attr('disabled', false).html('Upload');
                    var response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                    }
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error'
                    }).then(function() {
                        location.reload();
                    });
                    
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
                    var html = '<table class="table table-bordered table-striped">';
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