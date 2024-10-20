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
                        <h1 class="m-0 text-dark">Produk</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('product.newIndex') }}">Produk</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Produk</li>
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
                <form id="edit-product-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
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
                                                <select name="status" class="form-control" required>
                                                    <option value="1" {{ $product->status == '1' ? 'selected':'' }}>Publish</option>
                                                    <option value="0" {{ $product->status == '0' ? 'selected':'' }}>Draft</option>
                                                </select>
                                                <span class="text-danger">{{ $errors->first('status') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="category_id">Kategori</label>
                                                
                                                <!-- DATA KATEGORI DIGUNAKAN DISINI, SEHINGGA SETIAP PRODUK USER BISA MEMILIH KATEGORINYA -->
                                                <select name="category_id" class="form-control">
                                                    <option value="">Pilih</option>
                                                    @foreach ($category as $row)
                                                    <option value="{{ $row->id }}" {{ $product->category_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger">{{ $errors->first('category_id') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="name">Nama Produk</label>
                                                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                                <span class="text-danger">{{ $errors->first('name') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Deskripsi</label>
                                            
                                                <!-- TAMBAHKAN ID YANG NNTINYA DIGUNAKAN UTK MENGHUBUNGKAN DENGAN CKEDITOR -->
                                                <textarea name="description" id="description" class="form-control">{{ $product->description }}</textarea>
                                                <span class="text-danger">{{ $errors->first('description') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="promo" class="form-label">Kategori</label>
                                                <!-- DATA KATEGORI DIGUNAKAN DISINI, SEHINGGA SETIAP PRODUK USER BISA MEMILIH KATEGORINYA -->
                                                <select name="type" id="promo" class="form-control">
                                                    <option value="">Pilih Kategori</option>
                                                    <option value="promo" {{ $product->type === 'promo' ? 'selected' : '' }}>Promo</option>
                                                </select>
                                                <span class="text-danger">{{ $errors->first('promo') }}</span>
                                            </div>
                                            
                                            <div class="form-group" id="promo_price_group" style="{{ $product->type === 'promo' ? 'display: block;' : 'display: none;' }}">
                                                <label for="promo_price" class="form-label">Harga Promo</label>
                                                <input type="text" id="promo_price" name="promo_price" class="form-control" value="{{ $product->promo_price ?? '' }}" placeholder="Masukkan Harga Promo">
                                                <span class="text-danger">{{ $errors->first('promo_price') }}</span>
                                            </div>
                                            
                                            <div class="form-group" id="created_at_group" style="{{ $product->type === 'promo' ? 'display: block;' : 'display: none;' }}">
                                                <label for="created_at" class="form-label">Pilih Tanggal</label>
                                                <div id="created_at" class="pull-right" style="background: #fff; cursor: pointer; padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; width: auto;">
                                                    <i class="fa-regular fa-calendar-days"></i>&nbsp;
                                                    <span></span> <b class="caret"></b>
                                                </div>
                                            </div>
                                            <div class="input-estimate form-group">
                                                <input type="hidden" id="estimate-input-start" name="estimate_input_start" class="form-control" value="{{ $product->start_date }}" readonly>
                                                <input type="hidden" id="estimate-input-end" name="estimate_input_end" class="form-control" value="{{ $product->end_date }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="price">Harga</label>
                                                <input type="text" id="price" name="price" class="form-control" value="{{ $product->price }}" required>
                                                <span class="text-danger">{{ $errors->first('price') }}</span>
                                            </div>
                                            {{-- <div class="form-group">
                                                <label for="weight">Berat</label>
                                                <input type="number" name="weight" class="form-control" value="{{ $weight1 }}" required>
                                                <span class="text-danger">{{ $errors->first('weight') }}</span>
                                            </div> --}}
                                            <div class="form-group">
                                                <label for="weight">Berat</label>
                                                <input type="text" name="weight" class="form-control" value="{{ $product->weight }}" required>
                                                <span class="text-danger">{{ $errors->first('weight') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="stock" class="form-label">Stok</label>
                                                <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                                                <span class="text-danger">{{ $errors->first('stock') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="stock" class="form-label">Petunjuk Penyimpanan</label>
                                                <input type="text" name="storage_instructions" class="form-control" value="{{ $product->storage_instructions }}" placeholder="Masukkan Petunjuk Penyimpanan">
                                                <span class="text-danger">{{ $errors->first('storage_instructions') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="stock" class="form-label">Masa Penyimpanan</label>
                                                <input type="text" name="storage_period" class="form-control" value="{{ $product->storage_period }}" placeholder="Masukkan Masa Penyimpanan">
                                                <span class="text-danger">{{ $errors->first('storage_period') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="stock" class="form-label">Unit</label>
                                                <input type="text" name="units" class="form-control" value="{{ $product->units }}" placeholder="Masukkan Unit">
                                                <span class="text-danger">{{ $errors->first('units') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="packaging" class="form-label">Packaging</label>
                                                <input type="text" name="packaging" class="form-control" value="{{ $product->packaging }}" placeholder="Masukkan Kemasan">
                                                <span class="text-danger">{{ $errors->first('packaging') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="serving_suggestions" class="form-label">Saran Penyajian</label>
                                                <input type="text" name="serving_suggestions" class="form-control" value="{{ $product->serving_suggestions }}" placeholder="Masukkan Saran Penyajian">
                                                <span class="text-danger">{{ $errors->first('serving_suggestions') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="image">Foto Produk</label>
                                                <div class="mb-3">
                                                    <!--  TAMPILKAN GAMBAR SAAT INI -->
                                                    <img class="d-block img-fluid rounded mx-auto" style="width: 200px; height: 150px; object-fit: contain;" src="{{ asset('products/' . $product->image) }}" alt="{{ $product->name }}">
                                                </div>
                                                <input type="file" name="image" class="form-control">
                                                <span style="color: black;">*Biarkan kosong jika tidak ingin mengganti gambar</span>
                                                <span class="text-danger">{{ $errors->first('image') }}</span>
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
                        <!-- <div class="col-md-4">
                            <div class="card">
                                <div class="card-body loader-area">
                                    <div class="form-group">
                                        <label for="price">Harga</label>
                                        <input type="text" id="price" name="price" class="form-control" value="{{ $product->price }}" required>
                                        <span class="text-danger">{{ $errors->first('price') }}</span>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="weight">Berat</label>
                                        <input type="number" name="weight" class="form-control" value="{{ $weight1 }}" required>
                                        <span class="text-danger">{{ $errors->first('weight') }}</span>
                                    </div> --}}
                                    <div class="form-group">
                                        <label for="weight">Berat</label>
                                        <input type="text" name="weight" class="form-control" value="{{ $product->weight }}" required>
                                        <span class="text-danger">{{ $errors->first('weight') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock" class="form-label">Stok</label>
                                        <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                                        <span class="text-danger">{{ $errors->first('stock') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock" class="form-label">Petunjuk Penyimpanan</label>
                                        <input type="text" name="storage_instructions" class="form-control" value="{{ $product->storage_instructions }}" placeholder="Masukkan Petunjuk Penyimpanan">
                                        <span class="text-danger">{{ $errors->first('storage_instructions') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock" class="form-label">Masa Penyimpanan</label>
                                        <input type="text" name="storage_period" class="form-control" value="{{ $product->storage_period }}" placeholder="Masukkan Masa Penyimpanan">
                                        <span class="text-danger">{{ $errors->first('storage_period') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock" class="form-label">Unit</label>
                                        <input type="text" name="units" class="form-control" value="{{ $product->units }}" placeholder="Masukkan Unit">
                                        <span class="text-danger">{{ $errors->first('units') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="packaging" class="form-label">Packaging</label>
                                        <input type="text" name="packaging" class="form-control" value="{{ $product->packaging }}" placeholder="Masukkan Kemasan">
                                        <span class="text-danger">{{ $errors->first('packaging') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="serving_suggestions" class="form-label">Saran Penyajian</label>
                                        <input type="text" name="serving_suggestions" class="form-control" value="{{ $product->serving_suggestions }}" placeholder="Masukkan Saran Penyajian">
                                        <span class="text-danger">{{ $errors->first('serving_suggestions') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Foto Produk</label>
                                        <div class="mb-3">
                                            
                                            <img class="d-block img-fluid rounded mx-auto" style="width: 200px; height: 150px; object-fit: contain;" src="{{ asset('products/' . $product->image) }}" alt="{{ $product->name }}">
                                        </div>
                                        <input type="file" name="image" class="form-control">
                                        <span style="color: black;">*Biarkan kosong jika tidak ingin mengganti gambar</span>
                                        <span class="text-danger">{{ $errors->first('image') }}</span>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <button class="btn btn-primary float-right">Ubah</button>
                                    </div>
                                </div>
                            </div>
                        </div> -->
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
            let start = moment('{{ $product->start_date }}', 'YYYY-MM-DD HH:mm:ss').isValid() ? moment('{{ $product->start_date }}') : moment().startOf('month');
            let end = moment('{{ $product->end_date }}', 'YYYY-MM-DD HH:mm:ss').isValid() ? moment('{{ $product->end_date }}') : moment().endOf('month');

            function cb(start, end) {
                const formattedStart = start.locale('id').format('dddd, DD MMMM YYYY HH:mm');
                const formattedEnd = end.locale('id').format('dddd, DD MMMM YYYY HH:mm');
                const formattedRange = `${formattedStart} - ${formattedEnd}`;

                // Update display and hidden inputs
                $('#created_at span').text(formattedRange);
                $('#estimate-input-start').val(start.format('YYYY-MM-DD HH:mm:ss'));
                $('#estimate-input-end').val(end.format('YYYY-MM-DD HH:mm:ss'));
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
                let startDate = moment(picker.startDate).locale(`id`).format('dddd, DD MMMM YYYY HH:mm:ss');
                let endDate = moment(picker.endDate).locale(`id`).format('dddd, DD MMMM YYYY HH:mm:ss');
                
                $('#created_at span').html(startDate + ' - ' + endDate);

                // Set the hidden input values to the selected date range
                $('#estimate-input-start').val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));
                $('#estimate-input-end').val(picker.endDate.format('YYYY-MM-DD HH:mm:ss'));
            });
            
            $('#edit-product-form').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                formData.set('description', aboutEditor.getData());
                var actionUrl = "{{ route('product.newUpdate') }}";

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
                        });
                    },
                    success: function(response) {
                        $('.loader-area').unblock();
                        if (response.success == true) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: response.message,
                                icon: 'success',
                                timer: 2000, // Display for 2 seconds
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
                                timer: 2000, // Display for 2 seconds
                                showCancelButton: false,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $('.loader-area').unblock();
                        var response = JSON.parse(xhr.responseText);
						if (response.error) {
							errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
						}
                        Swal.fire({
                            title: 'Gagal',
                            text: errorMessage,
                            icon: 'error',
                            timer: 3000, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.reload(true);
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection