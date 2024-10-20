@extends('layouts.ecommerce')

@section('title')
    <title>Daftar Pembayaran - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Daftar Pembayaran</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <a href="{{ route('customer.orders') }}">Daftar Pembayaran</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
	<section class="login_box_area p_120">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    <div class="row">
                        @if (session('success'))
                            <input type="hidden" id="success-message" value="{{ session('success') }}">
                        @endif

                        @if (session('error'))
                            <input type="hidden" id="error-message" value="{{ session('error') }}">
                        @endif
						<div class="col-md-12">
							<div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mt-3">Daftar Pembayaran</h4>
                                </div>
								<div class="card-body">
									@if($orders->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Invoice</th>
                                                        <th>Penerima</th>
                                                        <th>Total</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($orders as $key => $row)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->locale('id')->translatedFormat('l, d F Y h:i') }}</td>
                                                            <td><span class="text-uppercase">{{ $row->invoice }}</span></td>
                                                            <td>{{ $row->customer_name }}</td>
                                                            <td>{{ 'Rp ' . number_format($row->total, 0, ',', '.') }}</td>
                    
                                                            <td>
                                                                <a class="btn btn-success btn-sm" href="{{ route('customer.paymentForm', $row->invoice) }}">Bayar Pesanan</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="float-right mt-3">
                                            {!! $orders->links() !!}
                                        </div>
                                    @else
                                        <h4 class="text-center">Tidak ada daftar pembayaran</h4>
                                    @endif
                                </div>
                                
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

    <!-- Lihat Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Produk Pesanan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Total</th>
                                    <th>Nomor Resi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="productDetails">
                                <!-- Details will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            // session
            var successMessage = $('#success-message').val();
            var errorMessage = $('#error-message').val();

            if (successMessage) {
				$.toast({
					heading: 'Berhasil',
					text: successMessage,
					showHideTransition: 'slide',
					icon: 'success',
					position: 'top-right',
					hideAfter: 3000
				});
            }

            if (errorMessage) {
				$.toast({
					heading: 'Gagal',
					text: errorMessage,
					showHideTransition: 'fade',
					icon: 'error',
					position: 'top-right',
					hideAfter: 3000
				});
            }
            
        });
    </script>
@endsection