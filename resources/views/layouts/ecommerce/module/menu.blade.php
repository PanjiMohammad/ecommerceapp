<ul class="nav navbar-nav center_nav pull-right">

    <li class="nav-item">
        <div class="nav-input-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" class="form-control nav-input" placeholder="Cari Produk..." id="searchInput">
        </div>
        <div id="suggestions" class="suggestions-container" style="display: none;"></div>
    </li>

    {{-- <li class="nav-item {{ Route::currentRouteName() == 'front.index' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('front.index') }}">Beranda</a>
    </li>
    <li class="nav-item {{ Route::currentRouteName() == 'front.product' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('front.product') }}">Produk</a>
    </li> --}}
    {{-- <li class="nav-item {{ Route::currentRouteName() == 'front.promo' ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('front.promo') }}">Promo</a>
    </li> --}}
    {{-- <li class="nav-item">
        <a class="nav-link" href="https://api.whatsapp.com/send?phone=6287889165715&amp;text=Halo%20gan,%20Saya%20butuh%20bantuan%20terkait%20">Bantuan</a>
    </li> --}}
</ul>
