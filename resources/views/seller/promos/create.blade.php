@extends('layouts.seller')

@section('title')
    <title>PROMO - Tambah Produk</title>
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">PROMO - Tambah Produk</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('promoProduct.newIndex') }}">Promo</a>
                        </li>
                        <li class="breadcrumb-item active">
                        PROMO - Tambah Produk
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
            <form id="form-promo-store" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('promoProduct.newIndex') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="1">Publish</option>
                                                <option value="0">Draft</option>
                                            </select>
                                            <span class="text-danger">{{ $errors->first('status') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="category_id">Kategori</label>
                                            
                                            <!-- DATA KATEGORI DIGUNAKAN DISINI, SEHINGGA SETIAP PRODUK USER BISA MEMILIH KATEGORINYA -->
                                            <select name="category_id" class="form-control">
                                                <option value="">Pilih Kategori</option>
                                                @foreach ($category as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('category_id') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nama Produk</label>
                                            <input type="text" name="name" class="form-control" placeholder="Masukkan Nama" required>
                                            <span class="text-danger">{{ $errors->first('name') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="image">Foto Produk</label>
                                            <input type="file" name="image" class="form-control" required>
                                            <span class="text-danger">{{ $errors->first('image') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Deskripsi</label>
                                  
                                    <!-- TAMBAHKAN ID YANG NNTINYA DIGUNAKAN UTK MENGHUBUNGKAN DENGAN CKEDITOR -->
                                    <textarea name="description" id="description" class="form-control"></textarea>
                                    <span class="text-danger">{{ $errors->first('description') }}</span>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price" class="form-label">Harga</label>
                                            <input type="text" id="price" name="price" class="form-control" placeholder="Masukkan Harga" required>
                                            <span class="text-danger">{{ $errors->first('price') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="weight">Berat</label>
                                            <input type="number" name="weight" class="form-control" placeholder="Masukkan Berat" required>
                                            <span class="text-danger">{{ $errors->first('weight') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="stock" class="form-label">Stok</label>
                                            <input type="number" name="stock" class="form-control" placeholder="Masukkan Stok" required>
                                            <span class="text-danger">{{ $errors->first('stock') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="date">Estimati Waktu</label>
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
                                <hr>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary float-right">Tambah</button>
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

            $('#price').mask('000.000.000.000.000', {reverse: true});

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

            $('#form-promo-store').submit(function(e){
                e.preventDefault();
                
                var formData = new FormData(this);

                // Get data from CKEditor
                formData.set('description', aboutEditor.getData());

                $.ajax({
                    url: "{{ route('promoProduct.newStore') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $.blockUI({ 
                            message: '<i class="fa fa-spinner"></i>',
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
                        $.unblockUI();
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('promoProduct.newIndex') }}";
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
                            text: 'Error - ' + errorMessage,
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
@endsection