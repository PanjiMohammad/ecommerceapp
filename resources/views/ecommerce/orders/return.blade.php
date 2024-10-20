@extends('layouts.ecommerce')

@section('title')
    <title>Return {{ $order->invoice }} - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Return <span class="text-uppercase">{{ $order->invoice }}</span></h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dasboard</a>
                        <a href="{{ route('customer.orders') }}">Return <span class="text-uppercase">{{ $order->invoice }}</span></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="login_box_area p_120">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h3 class="mt-2">Ringkasan Pengembalian</h3>
                        </div>
                        <div class="card-body loader-area">
                            <div class="mb-2 mt-2">
                                <h4>Rincian Produk</h4>
                                <hr>
                                <div style="display: flex; align-items: stretch;">
                                    @if (isset($detailOrder->product->image) && isset($detailOrder->product->name))
                                        <a title="Lihat Gambar {{ $detailOrder->product->image }}" href="{{ asset('/products/' . $detailOrder->product->image) }}" target="_blank">
                                            <div style="display: block; height: 100px; width: 100px; border: 1px solid transparent; border-radius: 5px;">
                                                <img src="{{ asset('/products/' . $detailOrder->product->image) }}" style="width: 100%; height: 100%; object-fit: contain; border-radius: 5px;" alt="{{ $detailOrder->product->name }}">
                                            </div>
                                        </a>
                                    @endif
                                    <div style="display: flex; flex-direction: column; justify-content: space-between; width: 100%">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            @if (isset($detailOrder->product->name))
                                                <div class="align-items-center ml-3">
                                                    <p style="margin: 0; font-size: 16px; text-align: left; flex: 1;" class="font-weight-bold">{{ $detailOrder->qty . ' x ' . $detailOrder->product->name }}</p>
                                                </div>
                                            @endif
                                            <div>
                                                <p style="margin: 0; font-size: 16px; text-align: left; white-space: nowrap;" class="font-weight-bold">
                                                    Rp {{ number_format($detailOrder['price'] * $detailOrder['qty'], 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                        @php
                                            $weight = $detailOrder['weight'];

                                            if (strpos($weight, '-') !== false) {
                                                // If the weight is a range, split it into an array
                                                $weights = explode('-', $weight);
                                                $minWeight = (float) trim($weights[0]);
                                                $maxWeight = (float) trim($weights[1]);

                                                // Check if the weights are >= 1000 to display in Kg
                                                $minWeightDisplay = $minWeight >= 1000 ? ($minWeight / 1000) : $minWeight;
                                                $maxWeightDisplay = $maxWeight >= 1000 ? ($maxWeight / 1000) . ' Kg' : $maxWeight . ' gram / pack';

                                                // Construct the display string
                                                $weightDisplay = $minWeightDisplay . ' - ' . $maxWeightDisplay;
                                            } else {
                                                // Single weight value
                                                $weightDisplay = $weight >= 1000 ? ($weight / 1000) . ' Kg' : $weight . ' gram / pack';
                                            }
                                        @endphp
                                        <p class="ml-3" style="margin-top: 15px; font-size: 16px; text-align: left; flex: 1;">{{ $weightDisplay }}</p>
                                        <p class="ml-3" style="font-size: 16px; text-align: left; flex: 1;">{{ $detailOrder->shipping_service }}</p>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Informasi Pengiriman</h4>
                                    <hr>
                                    <div class="table-responsive">
                                        <table>
                                            <tr>
                                                <td style="width: 130px; padding-right: 10px;"><p style="margin-bottom: 5px;">Nama Pembeli</p></td>
                                                <td style="padding-right: 10px;"><p style="margin-bottom: 5px;">:</p></td>
                                                <td style="padding-left: 10px;"><p style="margin-bottom: 5px;">{{ $customers->name }}</p></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 130px; padding-right: 10px;"><p style="margin-bottom: 5px;">Nomor Telepon</p></td>
                                                <td style="padding-right: 10px;"><p style="margin-bottom: 5px;">:</p></td>
                                                <td style="padding-left: 10px;"><p style="margin-bottom: 5px;">{{ $customers->phone_number }}</p></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 130px; padding-right: 10px;"><p style="margin-bottom: 5px;">Email</p></td>
                                                <td style="padding-right: 10px;"><p style="margin-bottom: 5px;">:</p></td>
                                                <td style="padding-left: 10px;"><p style="margin-bottom: 5px;">{{ $customers->email }}</p></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 130px; padding-right: 10px;"><p style="margin-bottom: 5px;">Alamat</p></td>
                                                <td style="padding-right: 10px;"><p style="margin-bottom: 5px;">:</p></td>
                                                <td style="padding-left: 10px;"><p style="margin-bottom: 5px;">{{ $customers->address . ', Kecamatan ' . $customers->district->name . ', Kota ' . $cities->name . ', ' . $provinces->name . ', Kode Pos ' . $cities->postal_code . ', Indonesia' }}</p></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>                                
                                <div class="col-md-6">
                                    <h4>Detail Pesanan</h4>
                                    <hr>
                                    <div class="table-responsive">
                                        <table>
                                            <tr>
                                                <td style="padding-right: 10px; width: 40%;"><p style="margin-bottom: 5px;">Status Pembayaran</p></td>
                                                <td style="padding-right: 10px; width: 5%;"><p style="margin-bottom: 5px;">:</p></td>
                                                <td style="padding-left: 10px;"><p style="margin-bottom: 5px;">{!! $payment->status_label !!}</p></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right: 10px;"><p style="margin-bottom: 5px;">Nomor Faktur</p></td>
                                                <td style="padding-right: 10px;"><p style="margin-bottom: 5px;">:</p></td>
                                                <td style="padding-left: 10px;"><p style="margin-bottom: 5px;">{{ $order->invoice }}</p></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right: 10px;"><p style="margin-bottom: 5px;">Nomor Resi</p></td>
                                                <td style="padding-right: 10px;"><p style="margin-bottom: 5px;">:</p></td>
                                                <td style="padding-left: 10px;" class="text-uppercase"><p style="margin-bottom: 5px;">{{ '#' . $detailOrder['tracking_number'] }}</p></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-right: 10px;">Tanggal Pembelian</td>
                                                <td style="padding-right: 10px;">:</td>
                                                <td style="padding-left: 10px;">{{ \Carbon\Carbon::parse($detailOrder['shop_date'])->locale('id')->translatedformat('l, d F Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <form id="return-form" action="{{ route('customer.return') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="product_id" value="{{ $detailOrder->product_id }}">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold" for="reason">Alasan Pengembalian</label>
                                    <textarea name="reason" placeholder="Masukkan Alasan" cols="5" rows="5" class="form-control" required></textarea>
                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-danger btn-md">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            // set timeout for session
            setTimeout(function() {
                $('.alert-success').remove();
                $('.alert-danger').remove();
            }, 2000);

            $('#return-form').submit(function (event) {
                event.preventDefault(); // Prevent the default form submission

                var formData = new FormData(this);

                bootbox.confirm({
					message: '<i class="fas fa-exclamation-triangle text-warning mr-2"></i> Kamu yakin mengembalikkan produk ini ?',
					backdrop: true,
					buttons: {
						confirm: {
							label: 'Ya <i class="fas fa-check ml-1"></i>',
							className: 'btn-success btn-sm'
						},
						cancel: {
							label: 'Tidak <i class="fas fa-xmark ml-1"></i>',
							className: 'btn-danger btn-sm'
						}
					},
					callback: function(result) {
						if(result) {
                            $.ajax({
                                url: $('#return-form').attr('action'),
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                beforeSend: function() {
                                    $('.loader-area').block({ 
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
                                complete: function() {
                                    $('.loader-area').unblock();
                                },
                                success: function (response) {
                                    if(response.success) {
                                        $.toast({
                                            heading: 'Berhasil',
                                            text: response.success,
                                            showHideTransition: 'slide',
                                            icon: 'success',
                                            position: 'top-right'
                                        });
                                        setTimeout(function() {
                                            window.location.href = "{{ route('customer.view_order', ['invoice' => $order->invoice]) }}";
                                        }, 2000);
                                    } else {
                                        $.toast({
                                            heading: 'Gagal',
                                            text: response.error,
                                            showHideTransition: 'fade',
                                            icon: 'error',
                                            position: 'top-right'
                                        });
                                    }
                                },
                                error: function (xhr, status, error) {
                                    var response = JSON.parse(xhr.responseText);
                                    if (response.error) {
                                        errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                    }
                                    $.toast({
                                        heading: 'Gagal',
                                        text: errorMessage,
                                        showHideTransition: 'fade',
                                        icon: 'error',
                                        position: 'top-right'
                                    });
                                }
                            });
                        }
                    }
                });
            });

        });
    </script>
@endsection