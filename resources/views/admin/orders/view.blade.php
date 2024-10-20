@extends('layouts.admin')

@section('title')
    <title>Detail Pesanan</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Detail Pesanan </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pesanan</a></li>
                            <li class="breadcrumb-item active">Detail Pesanan</li>
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
                                <a href="{{ route('orders.index') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                                <span class="float-right">Pesanan : <span class="text-uppercase font-weight-bold">#{{ $order->invoice }}</span></span>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                <h4 class="mb-3">Detail Pelanggan</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Nama Pelanggan</th>
                                        <td>{{ $order->customer_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Telpon</th>
                                        <td>{{ $order->customer_phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $order->customer->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ $order->customer_address }} {{ $order->customer->district->name }} - {{  $order->customer->district->city->name}}, {{ $order->customer->district->city->province->name }}</td>
                                    </tr>
                                </table>
                                <br>
                                <h2 class="mb-3">Rincian Produk</h2>
                                @foreach($details as $row)
                                    <div class="mb-4" style="border: 1px solid rgba(0, 0, 0, .125); padding: 15px;">
                                        <div class="product-info d-flex align-items-center justify-content-between">
                                            <div>
                                                <p>Status Produk : {!! $row->status_label !!}</p>
                                            </div>
                                            
                                            
                                            @if ($row->status == 1 && $row->status_payment == 0)
                                                <a href="javascript:void(0);" data-invoice="{{ $order->invoice }}" data-product-id="{{ $row->product_id }}" class="btn btn-success btn-sm approve-payment">Terima Pembayaran</a>
                                            @endif

                                            @if($row->status > 1)
                                                @if($row->status == 2)
                                                    <div class="d-flex justify-content-end">
                                                        <div class="form-group ml-2 mb-0">
                                                            <form id="processForm" action="{{ route('orders.newProcess') }}" method="post">
                                                                @csrf
                                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                <input type="hidden" name="product_id" value="{{ $row->product->id }}">
                                                                <button id="submitBtnProcess" class="btn btn-sm btn-secondary" style="margin-left: -2px;" type="submit">Proses Pesanan</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($row->status == 3)
                                                    <div class="d-flex justify-content-end">
                                                        <p class="mt-1 mb-1">Input Resi :</p>
                                                        <div class="form-group ml-2 mb-0">
                                                            <form id="shippingForm" action="{{ route('orders.newShipping') }}" method="post">
                                                                @csrf
                                                                <div class="input-group">
                                                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                    <input type="hidden" name="product_id" value="{{ $row->product->id }}">
                                                                    <input type="text" name="tracking_number" placeholder="Masukkan Resi" class="form-control">
                                                                    <div class="input-group-append">
                                                                        <button id="submitBtn" class="btn btn-secondary" style="margin-left: -2px;" type="submit">Kirim</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            @if($row->status_return != null)
                                                @if($row->status_return == 0 && $row->status ==  3)
                                                    <a href="{{ route('orders.newReturn', ['invoice' => $order->invoice, 'product_id' => $row->product_id]) }}">
                                                        <span class="btn btn-sm btn-outline-success ml-3">Permintaan Return</span>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h4 class="mb-3">Detail Pembayaran</h4>
                                                @if ($row->status_payment != 0)
                                                    <div class="payment-detail">
                                                        <table class="payment-table">
                                                            <tr>
                                                                <td class="label">Nama Pengirim</td>
                                                                <td class="separator">:</td>
                                                                <td class="value">{{ $row->payment['name'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="label">Bank Tujuan</td>
                                                                <td class="separator">:</td>
                                                                <td class="value">{{ $row->payment['transfer_to'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="label">Tanggal Transfer</td>
                                                                <td class="separator">:</td>
                                                                <td class="value">{{ $row->formatted_transfer_date }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="label">Bukti Pembayaran</td>
                                                                <td class="separator">:</td>
                                                                <td class="value">
                                                                    <a target="_blank" href="{{ asset('/proof/payment/' . $row->payment['proof']) }}" class="btn btn-primary btn-sm" style="margin-left: -0px;">Lihat <i class="fa fa-eye ml-1"></i></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="label">Status</td>
                                                                <td class="separator">:</td>
                                                                <td class="value">{!! $row->payment['status_label'] !!}</td>
                                                            </tr>
                                                            @if($row->status_return)
                                                                <tr>
                                                                    <td class="label">Return Status</td>
                                                                    <td class="separator">:</td>
                                                                    <td class="value">{!! $row->status_label_return !!}</td>
                                                                </tr>
                                                            @endif
                                                        </table>
                                                    </div>
                                                @else
                                                    <h5 class="text-center">Belum Konfirmasi Pembayaran</h5>
                                                @endif
                                            </div>
                                            <div class="col-md-8">
                                                <h4 class="mb-3">Detail Produk</h4>
                                                <div style="border: 1px solid rgba(0, 0, 0, .125); padding: 15px;">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <p class="mb-2">Id Produk : <span class="font-weight-bold">#{{ $row->product->id }}</span></p>
                                                        @if($row->tracking_number != null)
                                                            <p class="mb-2">Nomor Resi : <span class="font-weight-bold text-uppercase">#{{ $row->tracking_number }}</span></p>
                                                        @endif
                                                    </div>
                                                    {{-- <div class="d-flex justify-content-between">
                                                        <p class="mb-2">Status Detail Produk : <span class="badge badge-secondary">Baru</span></p>
                                                        <p class="mb-2">Status Detail Produk : <span class="badge badge-secondary">Baru</span></p>
                                                    </div> --}}
                                                    <table class="table table-no-bordered table-hover table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-left">Produk</th>
                                                                <th class="text-left">Harga</th>
                                                                <th class="text-right">Kuantiti</th>
                                                                <th class="text-right">Berat</th>
                                                                <th class="text-right">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="font-weight-bold">
                                                                <td class="text-left">
                                                                    <div class="d-flex align-items-center">
                                                                        <img class="rounded" height="80" width="100" src="{{ asset('/products/' . $row->product->image) }}" alt="{{ $row->product->name }}">
                                                                        <span class="ml-2">{{ $row->product->name }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="text-left" style="vertical-align: middle;">Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                                                                <td class="text-right" style="vertical-align: middle;">{{ $row->qty }}</td>
                                                                <td class="text-right" style="vertical-align: middle;">{{ $row->weight }}</td>
                                                                <td class="text-right" style="vertical-align: middle;">Rp {{ number_format($row->price * $row->qty, 0, ',', '.') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" class="text-left">Subtotal</td>
                                                                <td colspan="3" class="text-right">Rp {{ number_format($row->price * $row->qty, 0, ',', '.') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" class="text-left">Ongkos Kirim (Kurir : {{ $row->shipping_service }})</td>
                                                                <td colspan="3" class="text-right">Rp {{ number_format($row->shipping_cost, 0, ',', '.') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" class="text-left">PPn 10%</td>
                                                                <td colspan="3" class="text-right">Rp {{ number_format(($row->price * $row->qty) * 0.10, 0, ',', '.') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" class="text-left">Total</td>
                                                                <td colspan="3" class="text-right">Rp {{ number_format(($row->price * $row->qty) + (($row->price * $row->qty) * 0.10) + $row->shipping_cost, 0, ',', '.') }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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

            // set timeout for session
            setTimeout(function() {
                $('.alert-success').remove();
                $('.alert-danger').remove();
            }, 2000);

            // process order
            $('#processForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin memproses pesanan ini?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonColor: 'green',
                    cancelButtonText: 'Tidak',
                    confirmButtonText: 'Ya, Proses',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(this).attr('action'),
                            type: 'POST',
                            data: formData,
                            beforeSend: function() {
                                $.blockUI({ 
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
                            success: function(response) {
                                $.unblockUI();
                                if (response.success) {
                                    window.location.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message,
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                // Handle error
                                var errorMessage = xhr.status + ': ' + xhr.statusText;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message + errorMessage,
                                });
                            },
                        });
                    }
                });
            });

            // shipping order
            $('#submitBtn').click(function(e) {
                e.preventDefault();

                var formData = $('#shippingForm').serialize(); // Serialize form data

                $.ajax({
                    url: $('#shippingForm').attr('action'),
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $.blockUI({ 
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
                                    window.location.href = "{{ route('orders.newView', $order->invoice) }}";
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan! Silahkan Coba Lagi Nanti.',
                        });
                    }
                });
            });

            $('.approve-payment').on('click', function() {
                var invoice = $(this).data('invoice');
                var productId = $(this).data('product-id');
                var button = $(this);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menerima pembayaran ini?',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonColor: 'green',
                    cancelButtonText: 'Tidak',
                    confirmButtonText: 'Terima',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("orders.new_approve_payment", ["invoice" => ":invoice", "product_id" => ":product_id"]) }}'
                                .replace(':invoice', invoice)
                                .replace(':product_id', productId),
                            type: 'GET',
                            beforeSend: function() {
                                $.blockUI({ 
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
                                $.unblockUI();
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: 'Pembayaran Berhasil diterima.',
                                        icon: 'success'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "{{ route('orders.newView', $order->invoice) }}";
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Terjadi kesalahan.',
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan! Silakan coba lagi.',
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
        .product-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem; /* Optional: adjust space below the row */
        }

        .product-info p {
            margin: 0; /* Optional: adjust spacing between paragraphs */
        }

        .btn {
            margin-left: 1rem; /* Optional: adjust space between text and button */
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid rgba(0, 0, 0, .125);
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: white;
        }

        .payment-table td {
            padding: 8px;
            /* vertical-align: top; */
        }

        .payment-table .label {
            font-weight: bold;
            /* width: 30%; */
        }

        .payment-table .separator {
            width: 5%;
        }

        .payment-table .value {
            /* width: 65%; */
        }

        .payment-table .value a {
            display: inline-block;
            margin-top: 5px;
        }
        
    </style>
@endsection