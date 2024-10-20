@extends('layouts.ecommerce')

@section('title')
    <title>Pusat Belanja Sayuran Online</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
    <section class="home_banner_area">
        <div class="overlay"></div>
        <div class="banner_inner d-flex align-items-center">
            <div class="container">
                <div class="banner_content row">
                    <div class="offset-lg-2 col-lg-8">
                        <a class="white_bg_btn" href="{{ route('front.product') }}">Lihat Produk</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Home Banner Area =================-->

    <!--================Hot Deals Area =================-->
    @if(\App\Helpers\SettingsHelper::getHotDealsVisibility() === 'show')
        <section class="hot_deals_area section_gap">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hot_deal_box">
                            <img class="img-fluid" src="{{ asset('ecommerce/img/product/hot_deals/deal1.jpg') }}" alt="">
                            <div class="content">
                                <h2>Hot Deals of this Week</h2>
                                <p>shop now</p>
                            </div>
                            <a class="hot_deal_link" href="#"></a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hot_deal_box">
                            <img class="img-fluid" src="{{ asset('ecommerce/img/product/hot_deals/deal1.jpg') }}" alt="">
                            <div class="content">
                                <h2>Hot Deals of this Month</h2>
                                <p>shop now</p>
                            </div>
                            <a class="hot_deal_link" href="#"></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!--================End Hot Deals Area =================-->

    <!-- Promo Area -->
    <section id="promo-area" class="section-new-gap">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div id="flash-sale-timer" class="timer">
                    <span class="flash-sale-title" style="font-size: 16px; color: black;">Promo <span style="font-size: 16px; color: black;">Berakhir dalam</span></span>
                    <span id="days" class="time-box">00</span> :
                    <span id="hours" class="time-box">00</span> :
                    <span id="minutes" class="time-box">00</span> :
                    <span id="seconds" class="time-box">00</span>
                </div>
                <div class="float-right">
                    <a href="javascript:void(0);" id="see-all-link" class="see-all">Lihat Semua</a>
                </div>
            </div>
    
            <div class="promo-slider mt-3">
                <!-- Promo items will be dynamically rendered by JavaScript -->
            </div>
        </div>
    </section>    
    <!-- end Promo Area -->

    <!--================ Feature Product Area =================-->
    <section class="feature_product_area section_gap">
        <div class="container">
            <div class="main_title">
                <h2>Produk Terbaru</h2>
                <p>Tampil trendi dengan kumpulan produk kekinian kami.</p>
            </div>
            <div class="row" style="margin-top: -25px;">
                <div class="col-md-12">
                    <a href="{{ route('front.product') }}" class="float-right see-all-products">Lihat Semua Produk <i class="fa fa-arrow-right"></i></a>
                </div>
            </div> 
            <div class="custom-grid" id="product-list">
                @forelse ($products as $row)
                    <div class="card-wrapper">
                        <div class="card card-hover" style="width: 100%; height: 100%; padding: 0;">
                            @if($row->type === 'promo')
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
                            @else
                                @if($row->stock == 0)
                                    <div class="out-stock">
                                        <i class="fa-solid fa-xmark" style="font-size: 14px;"></i> Habis
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
                                    <p class="mb-1">Rp {{ number_format(($row->price), 0, ',', '.') }}</p>
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
                            @endif
                        </div>
                    </div>
                @empty
                    <h3 class="text-center">Tidak ada produk yang tersedia.</h3>
                @endforelse
            </div>
            <hr style="border: 2px solid #ededed; border-radius: 10px;">
            <!-- GENERATE PAGINATION PRODUK -->
            <div id="paginateLinks" class="float-right">
                {{ $products->links() }}
            </div>  
        </div>
    </section>
    <!--================End Feature Product Area =================-->
    
@endsection

@section('js')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        $(document).ready(function() {
            // Pass promos data to JavaScript
            var promos = @json($promos);
            
            // Function to update promo section
            function updatePromoSection(promos) {
                var promoContainer = $('.promo-slider');
                promoContainer.empty(); // Clear existing promos

                if (promos.length > 0) {
                    promos.forEach(function(promo) {
                        promoContainer.append(`
                            <div class="promo">
                                <img src="/products/${promo.image}" alt="${promo.name}" class="promo-img"/>
                                <div class="promo-details">
                                    <p class="font-weight-bold">${promo.name}</p>
                                    <p>Rp ${promo.promo_price.toLocaleString('id-ID')}</p>
                                </div>
                                <a class="cart-icon" href="/product/${promo.slug}">
                                    <i class="lnr lnr-cart"></i>
                                </a>
                            </div>
                        `);
                    });

                    // Initialize or reinitialize the slick slider
                    promoContainer.slick({
                        infinite: true,
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        prevArrow: '<button class="slick-prev slick-btn" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>',
                        nextArrow: '<button class="slick-next slick-btn" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>'
                    });
                } else {
                    promoContainer.append('<p>Tidak ada produk yang sedang promo.</p>');
                }
            }

            // See all link click event
            $('#see-all-link').on('click', function() {
                window.location.href = '{{ route('front.promo') }}';
            });

            if (promos.length === 0) {
                $('#promo-area').hide(); // Hide the promo section
                $('.feature_product_area').css({
                    'padding-top': '100px',
                    'padding-bottom': '100px'
                });
            } else {
                updatePromoSection(promos);

                // Initialize the countdown timer for the promo section
                if (promos.length > 0) {
                    var promo = promos[0]; // Get the first promo item
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
                }
            }

            // products
            var currentPage = 1;

            function fetchProducts() {
                var query = $('#search-input').val();
                var price = $('#price-sort').val();

                $.ajax({
                    url: '{{ route("front.index") }}',
                    type: 'GET',
                    data: {
                        page: currentPage
                    },
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
                    complete: function() {
                        $.unblockUI();
                    },
                    success: function(data) {
                        var hasProducts = $(data).find('.card-wrapper').length > 0;

                        // Update the product list
                        if (hasProducts) {
                            $('#product-list').html($(data).find('#product-list').html());
                            $('#product-list').addClass('custom-grid');
                        } else {
                            $('#product-list').html('<h3 class="text-center">Tidak ada produk yang tersedia.</h3>');
                            $('#product-list').removeClass('custom-grid');
                            $('#product-list').css({ "padding": "20px 0 10px 0" });
                        }

                        // Update pagination
                        $('#paginateLinks').html($(data).find('#paginateLinks').html());
                        
                        // Disable search input and price sort if no products
                        $('#search-input, #price-sort').prop('disabled', !hasProducts);
                    }
                });
            }

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                currentPage = $(this).attr('href').split('page=')[1];
                fetchProducts();
            });

            fetchProducts();

        });
    </script>
@endsection

@section('css')
    <style>
        .section-new-gap {
            margin: 20% 0 0 0; 
            background: linear-gradient(-70deg, #fa7c30 30%, rgba(0, 0, 0, 0) 30%), url('{{ asset('/ecommerce/img/breadcrumb/iya-2.jpg') }}');
            padding: 30px 0;    
        }

        .see-all-products {
            color: #000;
            font-size: 14px;
        }

        .see-all-products:hover {
            color: blue;
            font-size: 14px;
        }

        .timer {
            display: flex;
            align-items: center;
            font-size: 16px;
        }

        .time-box {
            background-color: lightblue;
            color: white;
            padding: 5px;
            margin: 0 5px;
            border-radius: 5px;
        }

        .promo-slider {
            position: relative;
        }

        /* Cart icon styling */
        .cart-icon {
            position: absolute;
            top: 50%;
            right: 0; /* Initially hidden within the card */
            transform: translateY(-50%);
            background-color: white;
            border-radius: 10%;
            padding: 10px;
            transition: right 0.3s ease; /* Smooth transition for sliding in */
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 16px; /* Adjust size as needed */
            opacity: 0; /* Initially hidden */
        }

        .promo {
            position: relative;
            border: 1px solid white;
            background: white;
            border-radius: 5px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 10px; /* Adjust margin for spacing between slides */
            transition: opacity 0.3s ease;
        }

        /* Show the cart icon when hovering over the card */
        .promo:hover .cart-icon {
            right: 42%; /* Slide in to visible position */
            opacity: 1; /* Show the icon */
        }

        /* Change background color on hover */
        .cart-icon:hover {
            background-color: blue;
            color: white; /* Change text color for contrast */
        }

        .promo-img {
            width: 110px;
            height: 100px;
            object-fit: contain;
            border-radius: 5px;
            display: block;
            margin: auto;
        }

        .promo-details {
            margin-top: 10px;
            text-align: center;
        }

        .promo-details p {
            margin: 0;
        }

        .promo-details .font-weight-bold {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .slick-prev, .slick-next {
            color: black;
            border: none;
            border-radius: 10%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            margin: 0 -10px;
            opacity: 1;
        }

        .slick-prev:hover, .slick-next:hover {
            background-color: black;
            opacity: 1;
        }

        .see-all {
            font-size: 16px;
            color: black; /* Change this to the desired color */
        }

        .see-all:hover {
            color: white; /* Change this to the desired color */
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

        .product-image {
            border-top-right-radius: 3px;
            border-top-left-radius: 3px;
            object-fit: contain; 
            width: 100%;
            height: 210px;
            display: block;
        }

        /* Responsive design */
        @media (max-width: 1200px) {
            .product-image {
                height: 210px; /* Slightly smaller height for medium screens */
            }
        }

        @media (max-width: 992px) {
            .product-image {
                height: 210px; /* Smaller height for tablets */
            }
        }

        @media (max-width: 680px) {
            .product-image {
                height: 607px; /* Set height to 607px for the specified screen width */
            }
        }

        @media (max-width: 768px) {
            .product-image {
                height: 210px; /* Smaller height for small tablets */
            }
        }

        @media (max-width: 576px) {
            .product-image {
                height: 120px; /* Smallest height for mobile devices */
            }
        }
    </style>
@endsection
