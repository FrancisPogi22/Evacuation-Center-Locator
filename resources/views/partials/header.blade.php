<header class="header-section">
    <div class="container-fluid">
        <div class="mobile-header">
            <button type="button" class="bi bi-list" id="btn-sidebar-mobile"></button>
        </div>
        <div class="dropdown">
            @auth
                @if (auth()->user()->organization == 'CDRRMO')
                    <div class="notification" id="notification-container">
                        <span class="badge" id="badge" hidden></span>
                        <button class="bi bi-bell-fill" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        </button>
                        <ul class="dropdown-menu">
                            @forelse (array_merge($notifications['incident']->toArray(), $notifications['hazard']->toArray()) as $notification)
                                <li>
                                    @if (isset($notification['description']) && $notification['description'])
                                        <a href="{{ route('incident.report', 'pending') }}" class="dropdown-item">
                                            <span>Resident report a incident:
                                                {{ $notification['description'] }}
                                            </span>
                                        </a>
                                    @else
                                        <a href="{{ route('manage.hazard.report') }}" class="dropdown-item">
                                            <span>Resident report a hazard:
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
            @endauth
            <div class="header-menu">
                <button class="bi bi-caret-down-fill" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" id="changeTheme">
                            <i class="bi bi-moon" id="themeIcon"></i><span id="themeText">Dark Mode</span></a></li>
                    @auth
                        <li><a class="changePasswordBtn dropdown-item" href="#changePasswordModal" data-bs-toggle="modal">
                                <i class="bi bi-shield-lock"></i>Change Password</a></li>
                        <li><a class="myAccount dropdown-item" href="{{ route('account.display.profile') }}">
                                <i class="bi bi-person-circle"></i>My Account</a></li>
                        <li id="logoutBtn"><a class="logout dropdown-item" href="{{ route('logout.user') }}">
                                <i class="bi bi-box-arrow-in-left"></i>Logout</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</header>
