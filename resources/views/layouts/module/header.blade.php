<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa-solid fa-bell" style="color: black;"></i>
            </a>
        </li>
        <hr>
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa fa-gear" style="color: black;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <h6 class="dropdown-divider">Pengaturan</h6>
                @if (Auth::guard('web')->check())
                    <div class="dropdown-item">
                        <a style="color: #000;" href="{{ route('user.acountSetting', Auth::guard('web')->user()->id) }}">
                            <span class="fa fa-gear mr-1"></span> {{ Auth::guard('web')->user()?->name }}
                        </a>
                    </div>
                @else
                    @if (Auth::guard('seller')->check())
                        <a class="dropdown-item" style="color: #000;"
                            href="{{ route('seller.setting', Auth::guard('seller')->user()->id) }}">
                            <i class="fa fa-gear mr-1"></i> {{ Auth::guard('seller')->user()?->name }}
                        </a>
                    @endif
                @endif
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" id="logout-button">
                    <i class="fas fa-sign-out-alt mr-1"></i> Keluar
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>