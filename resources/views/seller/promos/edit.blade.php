@extends('layouts.seller')

@section('title')
    <title>Edit Produk</title>
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">PROMO - Edit Produk</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('promoProduct.newIndex') }}">Promo</a>
                        </li>
                        <li class="breadcrumb-item active">PROMO - Edit Produk</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <!-- TAMBAHKAN ENCTYPE="" KETIKA MENGIRIMKAN FILE PADA FORM -->
            <form id="edit-promo-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="product_id" value="{{ $promo->id }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Produk</h4>
                            </div>
                            <div class="card-body loader-area">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="1" {{ $promo->status == '1' ? 'selected':'' }}>Publish</option>
                                                <option value="0" {{ $promo->status == '0' ? 'selected':'' }}>Draft</option>
                                            </select>
                                            <span class="text-danger" id="status_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="category_id">Kategori</label>
                                            
                                            <!-- DATA KATEGORI DIGUNAKAN DISINI, SEHINGGA SETIAP PRODUK USER BISA MEMILIH KATEGORINYA -->
                                            <select name="category_id" id="category_id" class="form-control">
                                                <option value="">Pilih</option>
                                                @foreach ($category as $row)
                                                    <option value="{{ $row->id }}" {{ $promo->category_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger" id="category_id_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Nama Produk</label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{ $promo->name }}" required>
                                            <span class="text-danger" id="name_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Deskripsi</label>
                                        
                                            <!-- TAMBAHKAN ID YANG NNTINYA DIGUNAKAN UTK MENGHUBUNGKAN DENGAN CKEDITOR -->
                                            <textarea name="description" id="description" class="form-control">{{ $promo->description }}</textarea>
                                            <span class="text-danger" id="description_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="promo" class="form-label">Kategori</label>
                                            <!-- DATA KATEGORI DIGUNAKAN DISINI, SEHINGGA SETIAP PRODUK USER BISA MEMILIH KATEGORINYA -->
                                            <select name="type" id="type" class="form-control">
                                                <option value="">Pilih Kategori</option>
                                                <option value="promo" {{ $promo->type === 'promo' ? 'selected' : '' }}>Promo</option>
                                            </select>
                                            <span class="text-danger" id="type_error"></span>
                                        </div>
                                        
                                        <div class="form-group" id="promo_price_group" style="{{ $promo->type === 'promo' ? 'display: block;' : 'display: none;' }}">
                                            <label for="promo_price" class="form-label">Harga Promo</label>
                                            <input type="text" id="promo_price" name="promo_price" class="form-control" value="{{ $promo->promo_price ?? '' }}" placeholder="Masukkan Harga Promo">
                                            <span class="text-danger" id="promo_price_error"></span>
                                        </div>
                                        
                                        <div class="form-group" id="created_at_group" style="{{ $promo->type === 'promo' ? 'display: block;' : 'display: none;' }}">
                                            <label for="created_at" class="form-label">Pilih Tanggal</label>
                                            <div id="created_at" class="pull-right" style="background: #fff; cursor: pointer; padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; width: auto;">
                                                <i class="fa-regular fa-calendar-days"></i>&nbsp;
                                                <span></span> <b class="caret"></b>
                                            </div>
                                        </div>
                                        <div class="input-estimate form-group">
                                            <input type="hidden" id="estimate-input-start" name="estimate_input_start" class="form-control" value="{{ $promo->start_date }}" readonly>
                                            <input type="hidden" id="estimate-input-end" name="estimate_input_end" class="form-control" value="{{ $promo->end_date }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="price">Harga</label>
                                            <input type="text" id="price" name="price" class="form-control" value="{{ $promo->price }}" readonly>
                                            <span class="text-danger" id="price_error"></span>
                                        </div>
                                        {{-- <div class="form-group">
                                            <label for="weight">Berat</label>
                                            <input type="number" name="weight" class="form-control" value="{{ $weight1 }}" required>
                                            <span class="text-danger">{{ $errors->first('weight') }}</span>
                                        </div> --}}
                                        <div class="form-group">
                                            <label for="weight">Berat</label>
                                            <input type="text" name="weight" id="weight" class="form-control" value="{{ $promo->weight }}" readonly>
                                            <span class="text-danger" id="weight_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Stok</label>
                                            <input type="number" name="stock" id="stock" class="form-control" value="{{ $promo->stock }}" placeholder="Masukkan Jumlah Stok" readonly>
                                            <span class="text-danger" id="stock_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Petunjuk Penyimpanan</label>
                                            <input type="text" name="storage_instructions" id="storage_instructions" class="form-control" placeholder="Masukkan Petunjuk Penyimpanan" value="{{ $promo->storage_instructions }}">
                                            <span class="text-danger" id="storage_instructions_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Masa Penyimpanan</label>
                                            <input type="text" name="storage_period" id="storage_period" class="form-control" placeholder="Masukkan Masa Penyimpanan" value="{{ $promo->storage_period }}">
                                            <span class="text-danger" id="storage_period_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Unit</label>
                                            <input type="text" name="units" id="units" class="form-control" placeholder="Masukkan Unit" value="{{ $promo->units }}">
                                            <span class="text-danger" id="units_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="packaging" class="form-label">Packaging</label>
                                            <input type="text" name="packaging" id="packaging" class="form-control" value="{{ $promo->packaging }}" placeholder="Masukkan Jenis Kemasan">
                                            <span class="text-danger" id="packaging_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="serving_suggestions" class="form-label">Saran Penyajian</label>
                                            <input type="text" name="serving_suggestions" id="serving_suggestions" class="form-control" value="{{ $promo->serving_suggestions }}" placeholder="Masukkan Saran Penyaajian">
                                            <span class="text-danger" id="serving_suggestions_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="image">Foto Produk</label>
                                            <div class="mb-3">
                                                <!--  TAMPILKAN GAMBAR SAAT INI -->
                                                <div style="height: 150px; width: 150px; border: 1px solid transparent; border-radius: 4px; display: block; margin: auto;">
                                                    <img style="width: 100%; height: 100%; object-fit: contain; border-radius: 4px;" src="{{ asset('products/' . $promo->image) }}" alt="{{ $promo->name }}">
                                                </div>
                                            </div>
                                            <input type="file" name="image" id="image" class="form-control">
                                            <span style="color: black;">*Biarkan kosong jika tidak ingin mengganti gambar</span>
                                            <span class="text-danger" id="image_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="form-group">
                                    <button class="btn btn-primary float-right">Ubah</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('js')
    <!-- LOAD CKEDITOR -->
    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {

            // Initialize CKEditor 5
            let aboutEditor;

            ClassicEditor
                .create(document.querySelector('#description'))
                .then(editor => {
                    aboutEditor = editor;
                })
                .catch(error => {
                    console.error('There was a problem initializing CKEditor:', error);
                });

            // convert promo price & price
            $('#price').mask('000.000.000.000.000', {reverse: true});
            $('#promo_price').mask('000.000.000.000.000', {reverse: true});

            // Initialize Moment.js with dates from the database
            let start = moment('{{ $promo->start_date }}');
            let end = moment('{{ $promo->end_date }}');

            // Function to update the displayed date range
            function cb(start, end) {
                $('#created_at span').html(start.locale('id').format('dddd, DD MMMM YYYY HH:mm') + ' - ' + end.locale('id').format('dddd, DD MMMM YYYY HH:mm'));
                // Update hidden inputs
                $('#estimate-input-start').val(start.locale('id').format('YYYY-MM-DD HH:mm:ss'));
                $('#estimate-input-end').val(end.locale('id').format('YYYY-MM-DD HH:mm:ss'));
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
                $('#estimate-input-start').val(picker.startDate.locale('id').format('YYYY-MM-DD HH:mm:ss'));
                $('#estimate-input-end').val(picker.endDate.locale('id').format('YYYY-MM-DD HH:mm:ss'));
            });
            
            $('#edit-promo-form').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                formData.set('description', aboutEditor.getData());
                var actionUrl = "{{ route('promoProduct.newUpdate') }}";

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
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
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.success,
                            icon: 'success',
                            timer: 1500, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = "{{ route('promoProduct.newIndex') }}";
                            }
                        });
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;

                        // Clear previous errors and input-error classes
                        $('.text-danger').text('');
                        $('.input-error').removeClass('input-error');

                        // Display error messages in SweetAlert
                        if (response.error) {
                            Swal.fire({
                                title: 'Error',
                                text: response.error,
                                icon: 'error',
                                showCancelButton: false,
                                showConfirmButton: false,
                                timer: 3000,
                                willClose: () => {
                                    // Loop through validation errors and show them in the form
                                    $.each(response.errors, function(key, error) {
                                        // Show error message next to the form field
                                        $('#' + key + '_error').text(error[0]);

                                        // Add error class to the input field
                                        $('#' + key).addClass('input-error');
                                    });

                                    // Retain input values
                                    $.each(response.input, function(key, value) {
                                        $('#' + key).val(value);
                                    });

                                    // Optionally clear the error messages after a delay (e.g., 3 seconds)
                                    setTimeout(function() {
                                        $('.text-danger').text('');
                                        $('.input-error').removeClass('input-error');
                                    }, 3000);
                                }
                            });
                        }
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