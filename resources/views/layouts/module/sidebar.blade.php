  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <!-- <a href="{{ route('home') }}" class="brand-link">
        <div class="image">
            <img src="{{ asset('ecommerce/img/logo_pasar_jaya.jpg')}}" alt="User Image" class="mh-100 mw-100 mx-auto d-block" style="height: 100px; width: 100%;">
        </div>
    </a> -->
    
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ asset('admin-lte/dist/img/rifkidev.jpg')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            @if(Auth::guard('web')->check())
                <a href="{{ route('home') }}" class="d-block">{{ Auth::guard('web')->user()->name }}</a>
            @else
                @if(Auth::guard('seller')->check())
                    <a href="{{ route('seller.dashboard') }}" class="d-block">{{ Auth::guard('seller')->user()->name }}</a>
                @endif
            @endif
        </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
            @if(Auth::guard('web')->check())
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{Request::path() == 'administrator' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">MANAJEMEN KEUANGAN</li>
                <li class="nav-item">
                    <a href="{{ route('withdraw.index') }}" class="nav-link {{Request::path() == 'administrator/withdrawals' ? 'active' : ''}}">
                        <i class="nav-icon fa-solid fa-wallet"></i>
                        <p>Penarikan Uang</p>
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a href="{{ route('seller.dashboard') }}" class="nav-link {{Request::path() == 'seller' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            @endif

            @if(Auth::guard('web')->check())
                <li class="nav-header">DATA MASTER</li>
                <li class="nav-item">
                    <a href="{{ route('consumen.index') }}" class="nav-link {{Request::path() == 'administrator/consumen' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Konsumen</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('seller.newIndex') }}" class="nav-link {{Request::path() == 'administrator/seller' ? 'active' : ''}}">
                        <i class="nav-icon fa fa-user-tag"></i>
                        <p>Penjual</p>
                    </a>
                </li>
            @endif

            <li class="nav-header">MANAJEMEN PRODUK</li>
            @if(Auth::guard('web')->check())
                <li class="nav-item">
                    <a href="{{ route('category.index') }}" class="nav-link {{Request::path() == 'administrator/category' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-folder"></i>
                        <p>Kategori</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('product.index') }}" class="nav-link {{Request::path() == 'administrator/product' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-carrot"></i>
                        <p>Produk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="nav-link {{Request::path() == 'administrator/orders' ? 'active' : ''}}">
                        <i class="nav-icon fa-solid fa-cart-shopping"></i>
                        <p>Pesanan</p>
                    </a>
                </li>

                <li class="nav-header">MANAJEMEN LAPORAN PESANAN</li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Laporan<i class="right fas fa-angle-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('report.order') }}" class="nav-link {{Request::path() == 'administrator/reports/order' ? 'active' : ''}}">
                                <i class="nav-icon fa-regular fa-file-lines"></i>
                                <p>Pesanan</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('report.return') }}" class="nav-link {{Request::path() == 'administrator/reports/return' ? 'active' : ''}}">
                                <i class="nav-icon fa-solid fa-file-lines"></i>
                                <p>Pengembalian Pesanan</p>
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                @if(Auth::guard('seller')->check())
                    {{-- <li class="nav-item">
                        <a href="{{ route('category.newIndex') }}" class="nav-link {{Request::path() == 'seller/category' ? 'active' : ''}}">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>Kategori</p>
                        </a>
                    </li> --}}
                    <!--  Update -->

                    <li class="nav-item">
                        <a href="{{ route('product.newIndex') }}" class="nav-link {{Request::path() == 'seller/product' ? 'active' : ''}}">
                            <i class="nav-icon fas fa-carrot"></i>
                            <p>Produk</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('promoProduct.newIndex') }}" class="nav-link {{Request::path() == 'seller/promo' ? 'active' : ''}}">
                            <i class="nav-icon fa-solid fa-tag"></i>
                            <p>Promo</p>
                        </a>
                    </li>

                    <li class="nav-header">MANAJEMEN KEUANGAN</li>
                    <li class="nav-item">
                        <a href="{{ route('withdrawals.index') }}" class="nav-link {{Request::path() == 'seller/withdraw' ? 'active' : ''}}">
                            <i class="nav-icon fa-solid fa-wallet"></i>
                            <p>Penarikan Dana</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('orders.incomes') }}" class="nav-link {{Request::path() == 'seller/incomes' ? 'active' : ''}}">
                            <i class="nav-icon fa-solid fa-money-bill-wave"></i>
                            <p>Pendapatan Saya</p>
                        </a>
                    </li>

                    <li class="nav-header">MANAJEMEN PESANAN</li>
                    <li class="nav-item">
                        <a href="{{ route('orders.newIndex') }}" class="nav-link {{Request::path() == 'seller/orders' ? 'active' : ''}}">
                            <i class="nav-icon fa-solid fa-cart-shopping"></i>
                            <p>Pesanan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('orders.finishIndex') }}" class="nav-link {{Request::path() == 'seller/orders/finish' ? 'active' : ''}}">
                            <i class="nav-icon fa-solid fa-square-check"></i>
                            <p>Pesanan Selesai</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('orders.cancelIndex') }}" class="nav-link {{Request::path() == 'seller/cancel' ? 'active' : ''}}">
                            <i class="nav-icon fa-solid fa-square-xmark"></i>
                            <p>Pesanan Dibatalkan</p>
                        </a>
                    </li>
                    
                    <!--  Update -->
                    <li class="nav-header">MANAJEMEN LAPORAN PESANAN</li>
                    <li class="nav-item">
                        <a href="{{ route('report.newOrder') }}" class="nav-link {{Request::path() == 'seller/reports/order' ? 'active' : ''}}">
                            {{-- <i class="nav-icon fa-regular fa-file"></i> --}}
                            <i class="nav-icon fa-regular fa-file-lines"></i>
                            <p>Pesanan</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('report.newReturn') }}" class="nav-link {{Request::path() == 'seller/reports/return' ? 'active' : ''}}">
                            {{-- <i class="fa-regular fa-file"></i> --}}
                            <i class="nav-icon fa-solid fa-file-lines"></i>
                            <p>Pengembalian Pesanan</p>
                        </a>
                    </li>
                    <!-- /Update -->

                    {{-- <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Laporan<i class="right fas fa-angle-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('report.newOrder') }}" class="nav-link {{Request::path() == 'seller/reports/order' ? 'active' : ''}}">
                                    <i class="nav-icon fa-regular fa-file"></i>
                                    <p>Pesanan</p>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('report.newReturn') }}" class="nav-link {{Request::path() == 'seller/reports/return' ? 'active' : ''}}">
                                    <i class="nav-icon fa-regular fa-file"></i>
                                    <p>Pengembalian Pesanan</p>
                                </a>
                            </li>
                        </ul>
                    </li> --}}
                @endif
            @endif
            @if(Auth::guard('web')->check())
                <li class="nav-header">PENGATURAN</li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Pengaturan<i class="right fas fa-angle-right"></i></p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                        <a href="{{ route('user.contentSetting') }}" class="nav-link">
                            <i class="nav-icon fa-solid fa-image"></i>
                            <p>Konten</p>
                        </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <!-- Main Sidebar Container -->