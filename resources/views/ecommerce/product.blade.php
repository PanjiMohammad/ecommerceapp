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
                    <h2>Jual Sayur Online</h2>
                    <div class="page_link">
                        <a href="{{ route('front.index') }}">Dashboard</a>
                        <a href="{{ route('front.product') }}">Produk</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Home Banner Area =================-->

    <!--================Category Product Area =================-->
    <section class="cat_product_area section_gap">
        <div class="container">
            <div class="row flex-row-reverse">
                <div class="col-lg-9">
                    <div class="product_top_bar">
                        <div class="left_dorp">
                            <form id="search-form" action="{{ route('front.product') }}" method="get">
                                <div class="input-group mb-12">
                                    <div class="p-1">
                                        <select name="price" id="price-sort" class="form-control" {{ !$hasProducts ? 'disabled' : '' }}>
                                            <option value="">Filter Harga</option>
                                            <option value="ASC">Murah ke Mahal</option>
                                            <option value="DESC">Mahal Ke Murah</option>
                                            <option value="promo_price">Lagi Promo</option>
                                        </select>
                                    </div>
                                    <div class="p-1">
                                        <input type="text" name="q" id="search-input" class="form-control" placeholder="Cari..." value="{{ request()->q }}" {{ !$hasProducts ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="custom-grid" id="product-list">
                        @forelse ($products as $row)
                            <div class="card-wrapper">
                                <div class="card card-hover" style="width: 100%; height: 96%; padding: 0;">
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
                                                        {{ $discount }}%
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
                                            <div class="cart-icon">
                                                <a href="{{ url('/product/' . $row->slug) }}" class="custom-btn">
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
                                            <div class="cart-icon">
                                                <a href="{{ url('/product/' . $row->slug) }}" class="custom-btn">
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
                <div class="col-lg-3">
                    <div class="left_sidebar_area">
                        <aside class="left_widgets cat_widgets">
                            <div class="l_w_title">
                                <h3>Kategori Produk</h3>
                            </div>
                            <div class="widgets_inner">
                                <ul class="list">
                                    <!-- PROSES LOOPING DATA KATEGORI -->
                                    @foreach ($categories as $category)
                                    <li>
                                        <!-- JIKA CHILDNYA ADA, MAKA KATEGORI INI AKAN MENG-EXPAND DATA DIBAWAHNYA -->
                                        <strong><a href="{{ route('front.product') }}">{{ $category->name }}</a></strong>
                                        
                                        <!-- PROSES LOOPING DATA CHILD KATEGORI -->
                                        @foreach ($category->child as $child)
                                        <ul class="list" style="display: block">
                                            <li>
                                                <strong><a href="javascript:void(0);" data-slug="{{ $child->slug }}" class="category-filter">{{ $child->name }}</a></strong>
                                            </li>
                                        </ul>
                                        @endforeach
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================End Category Product Area =================-->
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            var segments = window.location.pathname.split('/');
            var currentCategory = segments.length > 2 && segments[1] === 'category' ? segments[2] : '';
            var currentPage = 1;

            function fetchProducts() {
                var query = $('#search-input').val();
                var price = $('#price-sort').val();
                
                // Check if the current page is the product page
                var url = window.location.pathname.includes('/product')
                    ? '{{ route('front.product') }}'
                    : (currentCategory 
                        ? '{{ route('front.category', ':slug') }}'.replace(':slug', currentCategory)
                        : '{{ route('front.product') }}');

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        q: query,
                        price: price,
                        page: currentPage // Use the currentPage for pagination
                    },
                    beforeSend: function() {
                        $('#product-list').block({ 
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
                    complete: function() {
                        $('#product-list').unblock();
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

                        // Update the URL in the browser's address bar
                        updateUrl();
                    }
                });
            }

            function updateUrl() {
                var query = $('#search-input').val();
                var price = $('#price-sort').val();
                var newUrl = '';

                // Check if the current page is the product page
                if (window.location.pathname.includes('/product')) {
                    newUrl = '{{ url('/product') }}?page=' + currentPage;
                } else if (currentCategory) {
                    newUrl = '{{ url('/category') }}/' + currentCategory + '?page=' + currentPage;
                }

                if (query) {
                    newUrl += '&q=' + encodeURIComponent(query);
                }

                if (price) {
                    newUrl += '&price=' + encodeURIComponent(price);
                }

                history.pushState(null, null, newUrl);
            }

            function updateProducts() {
                currentPage = 1; // Reset to first page on new filter or search
                fetchProducts();
            }

            $('.category-filter').on('click', function() {
                currentPage = 1;
                currentCategory = $(this).data('slug');
                updateProducts();
            });

            $('#search-input').on('keyup', function() {
                updateProducts();
            });

            $('#price-sort').on('change', function() {
                console.log('Price sort changed:', $(this).val()); // Debugging line
                updateProducts();
            });


            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                if (page) {
                    currentPage = page;
                    fetchProducts();
                }
            });

            fetchProducts();
        });
    </script>
@endsection

@section('css')
    <style>
        .custom-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px; /* Adjust the gap between cards */
            margin-top: 20px;
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
            background-color: #777777;
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

        .cart-icon {
            position: absolute;
            top: 50%;
            right: -60px;
            transform: translateY(-50%);
            transition: right 0.3s ease, background-color 0.3s ease;
        }

        .card-hover:hover .cart-icon {
            right: 35%;
        }

        .custom-btn {
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

        .product-image {
            border-top-right-radius: 3px;
            border-top-left-radius: 3px;
            object-fit: contain; 
            width: 100%;
            height: 200px;
            margin: auto;
            display: block;
        }
    </style>
@endsection