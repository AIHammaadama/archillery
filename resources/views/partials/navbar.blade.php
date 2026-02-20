<!--**********************************
            Nav header start
        ***********************************-->
<div class="nav-header">
    <a href="{{ url('/') }}" class="brand-logo">
        <img class="logo-abbr" style="width: 400px; margin-right:-20px; margin-top: 5px;"
            src="{{ asset('/images/logo.png') }}" alt="">
        <img class="logo-compact" src="{{ asset('/images/logo-text.png') }}" alt="">
        <img class="brand-title" src="{{ asset('/images/logo-text.png') }}" alt="">
    </a>
</div>
<!--**********************************
            Nav header end
        ***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left"></div>
                <ul class="navbar-nav header-right">
                    @guest
                    @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="btn light btn-secondary" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @endif
                    @else
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:;" role="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <img @if(!empty(Auth::user()->photo)) src="{{ url('storage/'.Auth::user()->photo) }}" @else
                            src="https://www.pngfind.com/pngs/m/610-6104451_image-placeholder-png-user-profile-placeholder-image-png.png"
                            @endif width="20" alt="avatar">
                            <div class="header-info">
                                <span>Hello, <strong>{{ Auth::user()->firstname }}</strong></span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="{{ route('dashboard') }}" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-secondary"
                                    width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ml-2">Dashboard </span>
                            </a>
                            <a class="dropdown-item ai-icon" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span class="ml-2">{{ __('Logout') }} </span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                    @endguest
                </ul>
            </div>
        </nav>
    </div>
</div>