@extends('layouts.ecommerce')

@section('title')
    <title>Daftar Keinginan - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Daftar Keinginan</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <a href="{{ route('customer.wishlist') }}">Daftar Keinginan</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
	<section class="login_box_area p_100">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
				</div>
				<div class="col-md-9">
                    <div class="row">
						<div class="col-md-12">
							<div class="card">
                                <div class="card-header">
                                    <div class="mt-3">
                                        <h4 class="card-title">Daftar Keinginan</h4>
                                    </div>
                                </div>
								<div class="card-body loader-area">
                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif 
                                    
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif

                                    
									<div class="wishlist-container">
                                        <div class="table-responsive">
                                            {{-- <div class="wishlist-header">
                                                <h2>Wishlist</h2>
                                            </div> --}}
                                            <table class="wishlist-table table table-hover table-striped">
                                                {{-- <thead>
                                                    <tr>
                                                        <th colspan="2">Produk</th>
                                                        <th class="text-left">Harga</th>
                                                        <th class="text-left">Stok</th>
                                                        <th class="text-left">Opsi</th>
                                                    </tr>
                                                </thead> --}}
                                                <tbody>
                                                    @forelse($wishlists as $index => $row)
                                                        <tr>
                                                            <td>
                                                                <form id="delete-wishlist-form-{{ $index }}" class="delete-wishlist-form d-inline-block" action="{{ route('customer.deleteWishlist', $row->id) }}" method="post">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm btn-sm-wishlist" data-wishlist-id="{{ $row->id }}" title="Hapus Produk"><i class="fas fa-trash delete-btn text-white"></i></button>
                                                                </form>
                                                            </td>
                                                            <td>
                                                                @if($row->product)
                                                                    @php
                                                                        $weight = $row->product->weight;
                                
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
                                                                    <div class="d-flex align-items-center">
                                                                        <div style="width: 80px; height: 80px; display: block; border: 1px solid transparent;">
                                                                            <img src="{{ asset('/products/' . $row->product->image) }}" alt="{{ $row->product->image }}" style="border-radius: 4px; object-fit: contain; height: 100%; width: 100%;">
                                                                        </div>
                                                                        <div class="d-flex flex-column text-left ml-3">
                                                                            {{-- @if($row->product->type === 'promo')
                                                                                <span class="badge badge-danger text-capitalize">{{ $row->product->type }}</span>
                                                                            @else
                                                                                <span></span>
                                                                            @endif --}}
                                                                            <div class="d-flex align-items-center">
                                                                                <span class="font-weight-bold">{{ $row->product->name }}</span>
                                                                                <span class="badge badge-danger text-capitalize ml-2">{{ $row->product->type }}</span>
                                                                            </div>
                                                                            <span>{{ $weightDisplay }}</span>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <img height="200" width="auto" src="{{ asset('/products/default.jpg') }}" alt="Default Image">
                                                                @endif
                                                            </td>
                                                            <td class="text-left">
                                                                <div class="d-flex align-items-center">
                                                                    @php
                                                                        $price = (float) $row->product->price;
                                                                        $promoPrice = (float) $row->product->promo_price;
                                                                        $discount = round((($price - $promoPrice) / $price) * 100);
                                                                    @endphp
                                                                    @if($row->product->promo_price === null || $row->product->promo_price === 0)
                                                                        <span class="font-weight-bold">{{ 'Rp ' . number_format($row->product->price, 0, ',', '.') }}</span>
                                                                    @else
                                                                        <span>
                                                                            <span class="font-weight-bold">{{ 'Rp ' . number_format($row->product->promo_price, 0, ',', '.') }}</span>
                                                                            <span class="badge badge-info ml-1">{{ $discount . '%' }}</span>
                                                                        </span>
                                                                    @endif
                                                                    {{-- <span class="font-weight-bold">Rp {{ number_format($row->product->promo_price === null || $row->product->promo_price === 0 ? $row->product->price : $row->product->promo_price, 0, ',', '.') }}</span> --}}
                                                                </div>
                                                            </td>
                                                            {{-- <td>{{ $row->product->stock != null ? 'Stok Tersedia' : 'Stok Habis'}}</td> --}}
                                                            <td class="text-left">
                                                                @if($row->product->stock != null)
                                                                    <span class="text-success font-weight-bold">Stok Tersedia</span>
                                                                @else
                                                                    <span class="text-danger font-weight-bold">Stok Habis</span>
                                                                @endif
                                                            </td>
                                                            {{-- <td class="text-left">
                                                                @if($row->product->type === 'promo')
                                                                    <span class="badge badge-danger text-capitalize">{{ $row->product->type }}</span>
                                                                @else
                                                                    <span></span>
                                                                @endif
                                                            </td> --}}
                                                            <td class="text-left">
                                                                @if($row->product->stock > 0)
                                                                    <div class="d-flex align-items-center">
                                                                        {{-- <a href="{{ url('/product/'. $row->product->slug) }}" class="btn btn-secondary btn-sm" title="Lihat Produk">Detail <i class="fas fa-eye ml-1"></i></a> --}}
                                                                        <form id="add-to-cart-form-{{ $index }}" method="POST" class="d-inline-block">
                                                                            @csrf
                                                                            <input type="hidden" name="qty" value="1" title="Quantity:" class="form-control">
                                                                            <input type="hidden" name="product_id" value="{{ $row->product_id }}" class="form-control">   
                                                                            <button type="submit" class="btn btn-sm btn-primary" id="addToCart-{{ $index }}" title="Tambah Ke Keranjang"><i class="lnr lnr lnr-cart"></i></button>
                                                                        </form>
                                                                    </div>
                                                                @else
                                                                    <button class="btn btn-sm btn-primary" title="" disabled><i class="lnr lnr lnr-cart"></i></button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    <!-- Repeat similar rows for other products -->
                                                    @empty
                                                        <tr>
                                                            <td colspan="6">Tidak Ada Data</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="float-right">
                                        {!! $wishlists->links() !!}
                                    </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
    <script>
        $(document).ready(function(){

            @foreach($wishlist as $index => $row)
                // delete wishlist
                $('#delete-wishlist-form-{{ $index }}').on('submit', function(event) {
                    event.preventDefault();

                    var form = $(this);
                    var actionUrl = form.attr('action');

                    bootbox.confirm({
                        message: '<i class="fas fa-exclamation-triangle text-warning mr-2"></i> Kamu yakin ingin menghapus pesanan ini?',
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
                                    url: actionUrl,
                                    type: 'POST',
                                    data: form.serialize(),
                                    beforeSend: function() {
                                        $('loader-area').block({
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
                                    success: function(response) {
                                        $('loader-area').unblock();
                                        if(response.success) {
                                            $.toast({
                                                heading: 'Berhasil',
                                                text: response.success,
                                                showHideTransition: 'slide',
                                                icon: 'success',
                                                position: 'top-right',
                                                hideAfter: 3000
                                            });
                                            setTimeout(function() {
                                                location.reload(true);
                                            }, 1000);
                                        } else {
                                            $.toast({
                                                heading: 'Gagal',
                                                text: response.error,
                                                showHideTransition: 'fade',
                                                icon: 'error',
                                                position: 'top-right',
                                                hideAfter: 3000
                                            });
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        $('loader-area').unblock();
                                        var response = JSON.parse(xhr.responseText);
                                        if (response.error) {
                                            errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                        }
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
                            }
                        }
                    });
                });

                // add to cart
                $('#addToCart-{{ $index }}').click(function(e){
                    e.preventDefault();
                    var formData = $('#add-to-cart-form-{{ $index }}').serialize();

                    $.ajax({
                        url: "{{ route('front.cart') }}",
                        method: "POST",
                        data: formData,
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
                        complete: function () {
                            $('.loader-area').unblock();
                        },
                        success: function(response) {
                            if (response.success) {
                                $.toast({
                                    heading: 'Berhasil',
                                    text: response.success,
                                    showHideTransition: 'slide',
                                    icon: 'success',
                                    position: 'top-right',
                                    hideAfter: 1000
                                });
                                setTimeout(function() {
                                    location.reload(true);
                                }, 1000);
                            } else {
                                $.toast({
                                    heading: 'Gagal',
                                    text: response.error,
                                    showHideTransition: 'fade',
                                    icon: 'error',
                                    position: 'top-right',
                                    hideAfter: 3000
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                            }
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
                });
            @endforeach

        });
    </script>
@endsection

@section('css')
    <style>
        .wishlist-table {
            width: 100%;
        }
        .wishlist-table th, .wishlist-table td {
            text-align: center;
            vertical-align: middle;
        }
        .wishlist-table img {
            width: 125px;
            height: 100px;
            object-fit: cover;
        }
        .wishlist-table .add-to-cart-btn {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.2s;
        }
        .wishlist-table .add-to-cart-btn:hover {
            background-color: #555;
        }
        .wishlist-table .delete-btn {
            color: #e74c3c;
            cursor: pointer;
        }
    </style>
@endsection