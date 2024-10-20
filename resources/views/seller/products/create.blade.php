@extends('layouts.seller')

@section('title')
    <title>Tambah Produk</title>
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
                            <a href="{{ route('product.newIndex') }}">Produk</a>
                        </li>
                        <li class="breadcrumb-item active">
                        Tambah Produk
                        </li>
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
            <form id="form-product-store" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Tambah Produk</h4>
                            </div>
                            <div class="card-body loader-area">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Status</label>
                                            <select name="status" class="form-control">
                                                <option value="1">Publish</option>
                                                <option value="0">Draft</option>
                                            </select>
                                            <span class="text-danger" id="status_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="category_id" class="form-label">Kategori</label>
                                            <!-- DATA KATEGORI DIGUNAKAN DISINI, SEHINGGA SETIAP PRODUK USER BISA MEMILIH KATEGORINYA -->
                                            <select name="category_id" id="category_id" class="form-control">
                                                <option value="">Pilih Kategori</option>
                                                @foreach ($category as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger" id="category_id_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nama Produk</label>
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Nama">
                                            <span class="text-danger" id="name_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Deskripsi</label>
                                            <!-- TAMBAHKAN ID YANG NNTINYA DIGUNAKAN UTK MENGHUBUNGKAN DENGAN CKEDITOR -->
                                            <textarea name="description" id="description" class="form-control"></textarea>
                                            <span class="text-danger" id="description_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="promo" class="form-label">Tipe</label>
                                            <!-- DATA KATEGORI DIGUNAKAN DISINI, SEHINGGA SETIAP PRODUK USER BISA MEMILIH KATEGORINYA -->
                                            <select name="promo" id="promo" class="form-control">
                                                <option value="">Pilih Tipe</option>
                                                <option value="promo">Promo</option>
                                            </select>
                                            <span class="text-danger" id="promo_error"></span>
                                        </div>
                                        <div class="form-group" id="promo_price_group" style="display: none;">
                                            <label for="promo_price" class="form-label">Harga Promo</label>
                                            <input type="text" name="promo_price" id="promo_price" class="form-control" id="promo_price" placeholder="Masukkan Harga Promo">
                                            <span class="text-danger" id="promo_price_error"></span>
                                        </div>
                                        <div class="form-group" id="created_at_group" style="display: none;">
                                            <label for="created_at" class="form-label">Pilih Tanggal</label>
                                            <div id="created_at" class="pull-right" style="background: #fff; cursor: pointer; padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; width: auto;">
                                                <i class="fa-regular fa-calendar-days"></i>&nbsp;
                                                <span></span> <b class="caret"></b>
                                            </div>
                                        </div>
                                        <div class="input-estimate form-group">
                                            <input type="hidden" id="estimate-input-start" name="estimate_input_start" class="form-control" readonly>
                                            <input type="hidden" id="estimate-input-end" name="estimate_input_end" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="price" class="form-label">Harga</label>
                                            <input type="text" id="price" name="price" class="form-control" placeholder="Masukkan Harga">
                                            <span class="text-danger" id="price_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="weight">Berat</label>
                                            <input type="text" name="weight" id="weight" class="form-control" placeholder="Masukkan Berat">
                                            <span class="text-danger" id="weight_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Stok</label>
                                            <input type="number" name="stock" id="stock" class="form-control" placeholder="Masukkan Stok">
                                            <span class="text-danger" id="stock_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Petunjuk Penyimpanan</label>
                                            <input type="text" name="storage_instructions" class="form-control" placeholder="Masukkan Petunjuk Penyimpanan">
                                            <span class="text-danger">{{ $errors->first('storage_instructions') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Masa Penyimpanan</label>
                                            <input type="text" name="storage_period" class="form-control" placeholder="Masukkan Masa Penyimpanan">
                                            <span class="text-danger">{{ $errors->first('storage_period') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Unit</label>
                                            <input type="text" name="units" class="form-control" placeholder="Masukkan Masa Penyimpanan">
                                            <span class="text-danger">{{ $errors->first('units') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Packaging</label>
                                            <input type="text" name="packaging" class="form-control" placeholder="Masukkan Kemasan">
                                            <span class="text-danger">{{ $errors->first('packaging') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Saran Penyajian</label>
                                            <input type="text" name="serving_suggestions" class="form-control" placeholder="Masukkan Saran Penyajian">
                                            <span class="text-danger">{{ $errors->first('serving_suggestions') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="image">Foto Produk</label>
                                            <input type="file" name="image" id="image" class="form-control">
                                            <span class="text-danger" id="image_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary float-right">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="price" class="form-label">Harga</label>
                                    <input type="text" id="price" name="price" class="form-control" placeholder="Masukkan Harga" required>
                                    <span class="text-danger">{{ $errors->first('price') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="weight">Berat</label>
                                    <input type="text" name="weight" class="form-control" placeholder="Masukkan Berat" required>
                                    <span class="text-danger">{{ $errors->first('weight') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="stock" class="form-label">Stok</label>
                                    <input type="number" name="stock" class="form-control" placeholder="Masukkan Stok" required>
                                    <span class="text-danger">{{ $errors->first('stock') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="stock" class="form-label">Petunjuk Penyimpanan</label>
                                    <input type="text" name="storage_instructions" class="form-control" placeholder="Masukkan Petunjuk Penyimpanan">
                                    <span class="text-danger">{{ $errors->first('storage_instructions') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="stock" class="form-label">Masa Penyimpanan</label>
                                    <input type="text" name="storage_period" class="form-control" placeholder="Masukkan Masa Penyimpanan">
                                    <span class="text-danger">{{ $errors->first('storage_period') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="stock" class="form-label">Unit</label>
                                    <input type="text" name="units" class="form-control" placeholder="Masukkan Masa Penyimpanan">
                                    <span class="text-danger">{{ $errors->first('units') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="stock" class="form-label">Packaging</label>
                                    <input type="text" name="packaging" class="form-control" placeholder="Masukkan Kemasan">
                                    <span class="text-danger">{{ $errors->first('packaging') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="stock" class="form-label">Saran Penyajian</label>
                                    <input type="text" name="serving_suggestions" class="form-control" placeholder="Masukkan Saran Penyajian">
                                    <span class="text-danger">{{ $errors->first('serving_suggestions') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="image">Foto Produk</label>
                                    <input type="file" name="image" class="form-control" required>
                                    <span class="text-danger">{{ $errors->first('image') }}</span>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary float-right">Tambah</button>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </form>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('js')
    <!-- LOAD CKEDITOR -->
    {{-- <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script> --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/35.3.2/classic/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            
            // initialize CKEditor 5
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
            
            // jika kategorinya promo, maka muncul filter tanggal dan harga promo
            document.getElementById('promo').addEventListener('change', function() {
                var promoPriceGroup = document.getElementById('promo_price_group');
                var createdAtGroup = document.getElementById('created_at_group');

                if (this.value === 'promo') {
                    promoPriceGroup.style.display = 'block';
                    createdAtGroup.style.display = 'block';
                } else {
                    promoPriceGroup.style.display = 'none';
                    createdAtGroup.style.display = 'none';
                }
            });

            // daterangepicker
            let start = moment().startOf('month')
            let end = moment().endOf('month')

            function cb(start, end) {
                $('#created_at span').html(start.locale('id').format('dddd, DD MMMM YYYY') + ' - ' + end.locale('id').format('dddd, DD MMMM YYYY'));
            }

            $('#created_at').daterangepicker({
                startDate: start,
                endDate: end,
                locale: {
                    format: 'dddd, DD MMMM YYYY',
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

            $('#form-product-store').submit(function(e){
                e.preventDefault();

                var formData = new FormData(this);
                formData.set('description', aboutEditor.getData());

                $.ajax({
                    url: "{{ route('product.newStore') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.loader-area').block({ 
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
                        $('.loader-area').unblock();
                    },
                    success: function(response) {
                        if (response.success == true) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                willClose: () => {
                                    window.location.href = "{{ route('product.newIndex') }}";
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error',
                                timer: 2000,
                                showCancelButton: false,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let input = xhr.responseJSON.input;

                        // Clear previous errors
                        $('.text-danger').text('');

                        // response error
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
                                if(xhr.status == 500){
                                    window.location.reload(true);
                                } else {
                                    // Display validation errors using SweetAlert
                                    let errorMessage = '';
                                    $.each(errors, function(key, error) {
                                        errorMessage += error[0] + '<br>';
                                        $('#' + key + '_error').text(error[0]);

                                        $('#' + key).addClass('input-error');

                                        // Set timeout to clear the error text after 3 seconds
                                        setTimeout(function() {
                                            $('#' + key + '_error').text('');
                                            $('#' + key).removeClass('input-error');
                                        }, 3000);
                                    });

                                    // Retain input values
                                    $.each(input, function(key, value) {
                                        $('#' + key).val(value);
                                    });
                                }
                            }
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