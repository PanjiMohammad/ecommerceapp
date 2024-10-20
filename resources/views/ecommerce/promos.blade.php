@extends('layouts.ecommerce')

@section('title')
    <title>Jual Sayur Online - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
    <section class="banner_area">
        <div class="banner_inner d-flex align-items-center">
            <div class="container">
                <div class="banner_content text-center">
                    <h2>Promo Produk</h2>
                    <div class="page_link">
                        <a href="{{ route('front.index') }}">Dashboard</a>
                        <a href="{{ route('front.promo') }}">Promo Produk</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Home Banner Area =================-->

    <!--================Promo Product Area =================-->
    <section class="p_100">
        <div class="container">
            <div class="product_top_bar">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="left_dorp">
                        <form id="search-form" action="{{ route('front.product') }}" method="get">
                            <div class="input-group mb-12">
                                <div class="p-1">
                                    <select name="price" id="price-sort" class="form-control">
                                        <option value="">Filter Harga</option>
                                        <option value="ASC">Murah ke Mahal</option>
                                        <option value="DESC">Mahal Ke Murah</option>
                                        <option value="promo_price">Lagi Promo</option>
                                    </select>
                                </div>
                                <div class="p-1">
                                    <input type="text" name="q" id="search-input" class="form-control" placeholder="Cari..." value="{{ request()->q }}" >
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="flash-sale-timer" class="timer float-right">
                        <span class="flash-sale-title font-weight-bold">Promo <span>Berakhir dalam</span></span>
                        <span id="days" class="time-box">00</span> :
                        <span id="hours" class="time-box">00</span> :
                        <span id="minutes" class="time-box">00</span> :
                        <span id="seconds" class="time-box">00</span>
                    </div>
                </div>
            </div>
            {{-- <div class="promo-searchTimer">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex">
                        <form id="search-form" action="{{ route('front.promo') }}" method="get" class="d-flex">
                            <div class="custom-dropdown-container">
                                <div class="custom-dropdown-toggle">
                                    <span>Filter Harga</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="custom-dropdown-menu">
                                    <a href="#" class="custom-dropdown-item">Murah Ke Mahal</a>
                                    <a href="#" class="custom-dropdown-item">Mahal Ke Murah</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="price" id="price-sort" value="{{ request()->price }}">
                                <input type="text" name="q" id="search-input" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div> --}}
    
            <div class="custom-grid" id="product-list">
                @forelse ($promos as $row)
                    <div class="card-wrapper">
                        <div class="card card-hover" style="width: 100%; height: 100%; padding: 0;">
                            @if($row->stock == 0)
                                <div class="out-stock">
                                    <i class="fa-solid fa-xmark" style="font-size: 14px;"></i> Habis
                                </div>
                            @else
                                <div class="promo-label">
                                    <i class="fa-solid fa-tag" style="font-size: 14px;"></i> Promo
                                </div>
                            @endif
                            <img class="card-img-top d-block fade-on-hover product-image" src="{{ asset('/products/' . $row->image) }}" alt="{{ $row->name }}">
                            <div class="card-body d-flex flex-column fade-on-hover">
                                <p class="font-weight-bold mb-1">{{ $row->name }}</p>
                                @php
                                    $weight = $row->weight;

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
                                <p class="mb-1">{{ $weightDisplay }}</p>
                                <p class="mb-1">Rp {{ number_format(($row->promo_price), 0, ',', '.') }}</p>
                                @if(is_numeric($row->price) && is_numeric($row->promo_price))
                                    <div class="d-flex align-items-center" style="margin-bottom: -10px;">
                                        @php
                                            $price = (float) $row->price;
                                            $promoPrice = (float) $row->promo_price;
                                            $discount = round((($price - $promoPrice) / $price) * 100);
                                        @endphp
                                        <p class="badge badge-danger">
                                            {{ $discount }} %
                                        </p>
                                        <p class="ml-1" style="text-decoration: line-through;">
                                            {{ 'Rp ' . number_format($price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endif
                                {{-- <p class="card-text">
                                    <i class="fa fa-shop mr-1"></i> {{ $row->district_name }}, {{ $row->city_name }}
                                </p> --}}
                            </div>

                            @if($row->stock > 0)
                                <div class="cart-icon-2">
                                    <a href="{{ url('/product/' . $row->slug) }}" class="custom-btn-2">
                                        <i class="lnr lnr-cart"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <h3 class="text-center">Tidak ada produk yang tersedia.</h3>
                @endforelse
            </div>
        </div>
    </section>
    <!--================End Category Product Area =================-->
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            function fetchProducts() {
                var query = $('#search-input').val();
                var price = $('#price-sort').val();
                $.ajax({
                    url: '{{ route('front.promo') }}',
                    type: 'GET',
                    data: {
                        q: query,
                        price: price
                    },
                    success: function(data) {
                        // Replace the product list with the updated data
                        $('.latest_product_inner').html($(data).find('.latest_product_inner').html());
                        $('.right_page').html($(data).find('.right_page').html());
                        
                        // Check if there are products and enable/disable filters accordingly
                        var hasPromos = $(data).find('.latest_product_inner .col-md-12').length === 0;
                        $('#search-input, #price-sort').prop('disabled', !hasPromos);
                    }
                });
            }

            $('#search-input').on('keyup', function() {
                fetchProducts();
            });

            $('.custom-dropdown-item').on('click', function(e) {
                e.preventDefault();
                $('#price-sort').val($(this).data('price'));
                fetchProducts();
            });

            fetchProducts();

            // timer countdown
            var promos = @json($promos);
            if (promos.length > 0) {
                promos.forEach(function(promo) {
                    if (!promo.start_date || !promo.end_date) {
                        return; // Skip this promo if dates are not available
                    }

                    var startDate = new Date(promo.start_date).getTime();
                    var endDate = new Date(promo.end_date).getTime();

                    var now = new Date().getTime();
                    var countDownDate = now < startDate ? startDate : endDate;
                    var timerElement = $('#flash-sale-timer');

                    var x = setInterval(function() {
                        var now = new Date().getTime();
                        var distance = countDownDate - now;

                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        $('#days').text(("0" + days).slice(-2));
                        $('#hours').text(("0" + hours).slice(-2));
                        $('#minutes').text(("0" + minutes).slice(-2));
                        $('#seconds').text(("0" + seconds).slice(-2));

                        if (distance < 0) {
                            clearInterval(x);
                            $('#days').text("00");
                            $('#hours').text("00");
                            $('#minutes').text("00");
                            $('#seconds').text("00");
                            timerElement.text("EXPIRED");
                        }
                    }, 1000);
                });
            }
        });
    </script>
@endsection

@section('css')
    <style>
        .custom-dropdown-container {
            position: relative;
            display: inline-block;
        }

        .custom-dropdown-toggle {
            height: 40px;
            margin-right: 20px;
            border: 1px solid #777;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 210px;
            padding: 5px 10px;
            background-color: #f8f9fa;
            color: #333;
            font-size: 16px;
            transition: background-color 0.3s, color 0.3s;
            cursor: pointer;
        }

        .custom-dropdown-toggle:hover {
            background-color: #ededed;
            color: #777;
        }

        .custom-dropdown-toggle i {
            margin-left: 10px;
            transition: transform 0.3s;
        }

        .custom-dropdown-toggle:hover i {
            transform: translateX(5px);
        }

        .custom-dropdown-menu {
            display: none;
            position: absolute;
            background-color: #f8f9fa;
            min-width: 210px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
        }

        .custom-dropdown-item {
            padding: 5px 10px;
            color: #333;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s, color 0.3s;
        }

        .custom-dropdown-item:hover {
            background-color: #fff;
            color: #777;
        }

        .custom-dropdown-container:hover .custom-dropdown-menu {
            display: block;
        }

        .custom-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px; /* Adjust the gap between cards */
            margin-top: 20px;
        }

        /* Responsive design */
        @media (max-width: 1200px) {
            .custom-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .custom-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .custom-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .custom-grid {
                grid-template-columns: 1fr;
            }
        }

        .card-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .card-hover {
            position: relative;
            overflow: hidden;
            transition: background-color 0.3s ease;
        }

        .card-hover:hover {
            background-color: rgba(255, 255, 255, 0.7);
        }

        .fade-on-hover {
            transition: opacity 0.3s ease;
        }

        .card-hover:hover .fade-on-hover {
            opacity: 0.5;
        }

        .cart-icon-2 {
            position: absolute;
            top: 50%;
            right: -60px;
            transform: translateY(-50%);
            transition: right 0.3s ease, background-color 0.3s ease;
        }

        .card-hover:hover .cart-icon-2 {
            right: 37%;
        }

        .custom-btn-2 {
            padding: 10px 20px;
            font-size: 18px;
            color: #000;
            background-color: #fff;
            border: none;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .custom-btn-2:hover {
            background-color: #007bff;
            color: #fff;
        }

        .card-hover:hover .cart-icon-2 {
            background-color: #fff;
            border-radius: 5px;
        }

        .card-hover:hover .cart-icon-2 .custom-btn-2:hover {
            background-color: #007bff;
            color: #fff;
        }

        .promo-label {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: red;
            color: white;
            padding: 5px 10px;
            /* font-size: 14px; */
            font-weight: bold;
            border-radius: 5px;
            z-index: 10;
        }

        .out-stock {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: grey;
            color: white;
            padding: 5px 10px;
            /* font-size: 14px; */
            font-weight: bold;
            border-radius: 5px;
            z-index: 10;
        }

        .custom-btn {
            padding: 10px 20px;
            font-size: 18px;
            color: #007bff;
            background-color: #fff;
            border: none;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .custom-btn:hover {
            background-color: #007bff;
            color: #fff;
        }

        .card-hover:hover .cart-icon {
            background-color: #fff;
            border-radius: 5px;
        }

        .card-hover:hover .cart-icon .custom-btn:hover {
            background-color: #007bff;
            color: #fff;
        }
    </style>
@endsection
