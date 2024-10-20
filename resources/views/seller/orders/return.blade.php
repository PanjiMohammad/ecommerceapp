@extends('layouts.seller')

@section('title')
    <title>Detail Pengembalian Pesanan</title>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Detail Pengembalian Pesanan</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('orders.newIndex') }}">Pesanan</a></li>
                        <li class="breadcrumb-item active">Detail Pengembalian Pesanan</li>
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
                            <a href="{{ route('orders.newView', $order->invoice) }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                            <span class="float-right">Pesanan : <span class="text-uppercase font-weight-bold">#{{ $order->invoice }}</span></span>
                        </div>
                        <div class="card-body loader-area">
                            <form id="approveReturn">
                                @csrf
                                <div class="mb-4">
                                    <h4 class="mb-3">Detail Pelanggan</h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Nama Pelanggan</th>
                                            <td>{{ $order->customer_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Telepon Pelanggan</th>
                                            <td>{{ $order->customer_phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email Pelanggan</th>
                                            <td>{{ $order->customer->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat Pelanggan</th>
                                            <td>{{ $order->customer_address . ', Kec. ' . $order->customer->district->name . ', Kota ' . $order->customer->district->city->name . ', ' . $order->customer->district->city->province->name }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <hr>
                                <div class="row">
                                    <!-- BLOCK UNTUK MENAMPILKAN DATA PELANGGAN -->
                                    <div class="col-md-8">
                                        <h4 class="mb-3">Detail Produk</h4>
                                        <table class="table table-hover table-bordered table-striped">
                                            <tr>
                                                <th width="40%">Nomor Resi</th>
                                                <td class="font-weight-bold">{{ '#' . $detailOrder->tracking_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nama Produk</th>
                                                <td>{{ $detailOrder->product->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Kuantiti</th>
                                                <td>{{ $returns->qty . ' item' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Alasan Return</th>
                                                <td>{{ $returns->reason }}</td>
                                            </tr>
                                            <tr>
                                                <th>Jumlah Pengembalian Dana</th>
                                                <td class="refund">Rp {{ number_format($detailOrder->qty * $detailOrder->price, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>{!! $returns->status_label !!}</td>
                                            </tr>
                                        </table>
                                        
                                    </div>
                                    <div class="col-md-4">
                                        <h4 class="mb-3">Foto Produk</h4>
                                        <div class="d-flex justify-content-center" style="vertical-align: middle !important;">
                                            <img src="{{ asset('/products/' . $detailOrder->product->image) }}" class="img-responsive" height="200" alt="{{ $detailOrder->product->name }}">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="input-group">
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <input type="hidden" name="product_id" value="{{ $detailOrder->product_id }}">
                                    <input type="hidden" name="qty" value="{{ $detailOrder->qty }}">
                                    <hr>
                                    <div class="input-group-prepend">
                                        <button class="btn btn-primary" type="submit">Proses</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#approveReturn').on('submit', function(e) {
                e.preventDefault();

                var formData = $('#approveReturn').serialize();
                console.log(formData);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin melanjutkan proses ini?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonColor: 'green',
                    cancelButtonText: 'Tidak',
                    confirmButtonText: 'Ya, Lanjut',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('orders.new_approve_return') }}",
                            method: "POST",
                            data: formData,
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
                                console.log(response);
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.success,
                                        icon: 'success',
                                        timer: 2000,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            window.location.href = "{{ route('orders.newView', $order->invoice) }}";
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: response.error,
                                        icon: 'error',
                                        timer: 3000,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            window.location.reload(true);
                                        }
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
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
        });
    </script>
@endsection

@section('css')
    <style>
        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .img-responsive {
            max-width: 100%; /* Ensures the image doesn't overflow its container */
            height: auto;   /* Maintains aspect ratio */
            object-fit: contain;
            display: block;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
    </style>
@endsection