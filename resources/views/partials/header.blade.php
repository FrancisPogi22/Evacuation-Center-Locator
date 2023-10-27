<header class="header-section">
    <div class="container-fluid">
        <div class="mobile-header">
            <button type="button" class="bi bi-list" id="btn-sidebar-mobile"></button>
        </div>
        @auth
            <div class="dropdown">
                @if (auth()->user()->organization == 'CDRRMO')
                    <div class="notification" id="notification-container">
                        <button class="bi bi-bell-fill" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        </button>
                        <ul class="dropdown-menu">
                            @forelse (array_merge($notifications['incident']->toArray(), $notifications['area']->toArray()) as $notification)
                                <li>
                                    @if (isset($notification['details']) && $notification['details'])
                                        <a href="{{ route('manage.report', 'manage', '','') }}" class="dropdown-item">
                                            <p>Resident report a incident:
                                                {{ $notification['details'] }}
                                            </p>
                                            <span class="report_time">
                                                {{ $notification['report_time'] }}
                                            </span>
                                        </a>
                                    @else
                                        <a href="{{ route('manage.report', 'manage') }}" class="dropdown-item">
                                            <span>Resident report an area:
                                                {{ $notification['type'] }}
                                            </span>
                                        </a>
                                    @endif
                                </li>
                            @empty
                                <div class="empty-notification">No notification</div>
                            @endforelse
                        </ul>
                    </div>
                @endif
                <div class="header-menu">
                    <button class="bi bi-caret-down-fill" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item changeTheme">
                                <i class="bi bi-moon" id="themeIcon"></i><span id="themeText">Dark Mode</span></a></li>
                        <li><a class="changePasswordBtn dropdown-item" href="#changePasswordModal" data-bs-toggle="modal">
                                <i class="bi bi-shield-lock"></i>Change Password</a></li>
                        <li><a class="myAccount dropdown-item" href="{{ route('display.profile') }}">
                                <i class="bi bi-person-circle"></i>My Account</a></li>
                        <li id="logoutBtn"><a class="logout dropdown-item" href="{{ route('logout.user') }}">
                                <i class="bi bi-box-arrow-in-left"></i>Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        @endauth
        @guest
            <button class="changeTheme">
                <i class="bi bi-sun-fill" id="themeIconResident"></i>
            </button>
        @endguest
    </div>
</header>
