<div class="card" style="background-color: #f9f9ff">
    <div class="card-header">
        <h3 style="margin-top: 10px;">Main Menu</h3>
    </div> 
    <div class="card-body">
        
        <!-- <ul class="menu-sidebar-area">
            <li class="icon-dashboard">
                <a href="{{ route('customer.dashboard') }}">Dashboard</a>
            </li>
            <li class="icon-customers">
                <a href="{{ route('customer.orders') }}">Pesanan</a>
            </li>
            <li class="icon-wishlists">
                <a href="{{ route('customer.wishlist') }}">Wishlists</a>
            </li>
            <li class="icon-users">
                <a href="{{ route('customer.settingForm') }}">Pengaturan</a>
            </li>
        </ul> -->
        <ul class="list-group">
            <li class="list-group-item">
                <a href="{{ route('customer.dashboard') }}" style="color: #777777;">
                    <i class="fa-solid fa-house mr-1"></i>
                    <span class="font-weight-bold">Beranda</span>
                </a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('customer.orders') }}" style="color: #777777;">
                    <span class="fa-solid fa-cart-shopping mr-1"></span>
                    <span class="font-weight-bold">Pesanan <span class="badge badge-primary">{{ $newOrdersCount > 0 ? $newOrdersCount : '' }}</span></span>
                </a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('customer.wishlist') }}" style="color: #777777;">
                    <i class="fa-solid fa-heart mr-1"></i>
                    <span class="font-weight-bold">Daftar Keinginan</span>
                </a>
            </li>
            <li class="list-group-item">
                <a href="{{ route('customer.listPayment') }}" style="color: #777777;">
                    <span class="fa-solid fa-cart-shopping mr-1"></span>
                    <span class="font-weight-bold">Pembayaran <span class="badge badge-primary">{{ $newOrdersCount > 0 ? $newOrdersCount : '' }}</span></span>
                </a>
            </li>
            {{-- <li class="list-group-item">
                <a href="{{ route('customer.settingForm') }}" style="color: #777777;">
                    <i class="fa-solid fa-gear mr-1"></i>
                    <span class="font-weight-bold">Pengaturan</span>
                </a>
            </li> --}}
            <li class="list-group-item">
                <a href="{{ route('customer.history') }}" style="color: #777777;">
                    <i class="fa-regular fa-rectangle-list mr-1"></i>
                    <span class="font-weight-bold">Riwayat Pesanan</span>
                </a>
            </li>
        </ul>
    </div>
</div>