@extends('layouts.admin')

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

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Produk</h4>
                            </div>
                            <div class="card-body">
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
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
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
                                    <label for="price">Harga</label>
                                    <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
                                    <span class="text-danger">{{ $errors->first('price') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="weight">Berat</label>
                                    <input type="number" name="weight" class="form-control" value="{{ $product->weight }}" required>
                                    <span class="text-danger">{{ $errors->first('weight') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="stock" class="form-label">Stok</label>
                                    <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                                    <span class="text-danger">{{ $errors->first('stock') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="image">Foto Produk</label>
                                    <br>
                                  	<div class="text-center">
                                        <!--  TAMPILKAN GAMBAR SAAT INI -->
                                        <img src="{{ asset('products/' . $product->image) }}" width="200px" height="150px" alt="{{ $product->name }}">
                                    </div>
                                    <hr>
                                    <input type="file" name="image" class="form-control">
                                    <span style="color: black;">*Biarkan kosong jika tidak ingin mengganti gambar</span>
                                    <span class="text-danger">{{ $errors->first('image') }}</span>
                                </div>
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
    <script src="https://cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description');

        $(document).ready(function() {
            
            $('#edit-product-form').on('submit', function(e) {
                e.preventDefault();

                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }

                var formData = new FormData(this);
                var actionUrl = "{{ route('product.update', $product->id) }}";

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
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
                    error: function(response) {
                        $.unblockUI();
                        var errors = response.responseJSON.errors;
                        for (var key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                $('#' + key + '-error').text(errors[key][0]);
                            }
                        }
                    }
                });
            });

        });
    </script>
@endsection