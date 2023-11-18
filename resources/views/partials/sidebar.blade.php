<aside class="sidebar">
    <div class="sidebar-header">
        <img id="logo" src="{{ asset('assets/img/E-LIGTAS-Logo-Black.png') }}" alt="Logo">
        <button type="button" class="bi bi-x" id="btn-sidebar-close"></button>
    </div>
    <div class="sidebar-content">
        <div class="user-details">
            <div class="details-content">
                @auth
                    <img src="{{ asset('assets/img/' . auth()->user()->organization . '-LOGO.png') }}" alt="Logo">
                    <span>{{ auth()->user()->organization }}</span>
                @endauth
                @guest
                    <span class="py-2">Cabuyao Resident</span>
                @endguest
            </div>
        </div>
        <div class="text-center">
            <ul class="nav_list">
                @auth
                    @if (auth()->user()->organization == 'CDRRMO')
                        <div class="navigation-item">
                            <a href="{{ route('dashboard.cdrrmo') }}" class="menu-link">
                                <i class="bi bi-speedometer2"></i>
                                <span class="links_name">Dashboard</span>
                            </a>
                        </div>
                        <div class="navigation-item">
                            <a href="{{ route('eligtas.guideline') }}" class="menu-link">
                                <i class="bi bi-book"></i>
                                <span class="links_name">E-LIGTAS Guideline</span>
                            </a>
                        </div>
                        @if (auth()->user()->position == 'President')
                            <div class="navigation-item">
                                <a class="sub-btn">
                                    <i class="bi bi-people"></i>
                                    <span class="links_name">Users Account</span>
                                    <i class="bi bi-caret-right-fill dropdown"></i>
                                </a>
                                <div class="sub-menu">
                                    <a href="{{ route('display.users.account', 'active') }}" class="menu-link">
                                        <i class="bi bi-person-gear"></i>
                                        <span class="links_name">Manage Account</span>
                                    </a>
                                    <a href="{{ route('display.users.account', 'archived') }}" class="menu-link">
                                        <i class="bi bi-person-slash"></i>
                                        <span class="links_name">Archived Account</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                        <div class="navigation-item">
                            <a class="sub-btn">
                                <i class="bi bi-megaphone"></i>
                                <span class="links_name">Resident Report</span>
                                <i class="bi bi-caret-right-fill dropdown"></i>
                            </a>
                            <div class="sub-menu">
                                <a href="{{ route('manage.report', 'manage') }}" class="menu-link">
                                    <i class="bi bi-flag"></i>
                                    <span class="links_name">Manage Report</span>
                                </a>
                                <a href="{{ route('manage.report', 'archived') }}" class="menu-link">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                    <span class="links_name">Archived Report</span>
                                </a>
                            </div>
                        </div>
                        <div class="navigation-item">
                            <a href="{{ route('hotline.number') }}" class="menu-link">
                                <i class="bi bi-telephone"></i>
                                <span class="links_name">Hotline Numbers</span>
                            </a>
                        </div>
                    @elseif (auth()->user()->organization == 'CSWD')
                        <div class="navigation-item">
                            <a href="{{ route('dashboard.cswd') }}" class="menu-link">
                                <i class="bi bi-speedometer2"></i>
                                <span class="links_name">Dashboard</span>
                            </a>
                        </div>
                        <div class="navigation-item">
                            <a href="{{ route('eligtas.guideline') }}" class="menu-link">
                                <i class="bi bi-book"></i>
                                <span class="links_name">E-LIGTAS Guideline</span>
                            </a>
                        </div>
                        <div class="navigation-item">
                            <a class="sub-btn">
                                <i class="bi bi-clouds"></i>
                                <span class="links_name">Disaster</span>
                                <i class="bi bi-caret-right-fill dropdown"></i>
                            </a>
                            <div class="sub-menu">
                                <a href="{{ route('disaster.information', 'manage') }}" class="menu-link">
                                    <i class="bi bi-cloud-upload"></i>
                                    <span class="links_name">Manage Disaster</span>
                                </a>
                                <a href="{{ route('disaster.information', 'archived') }}" class="menu-link">
                                    <i class="bi bi-cloud-slash"></i>
                                    <span class="links_name">Archived Disaster</span>
                                </a>
                            </div>
                        </div>
                        <div class="navigation-item">
                            <a class="sub-btn">
                                <i class="bi bi-people"></i>
                                <span class="links_name">Evacuee</span>
                                <i class="bi bi-caret-right-fill dropdown"></i>
                            </a>
                            <div class="sub-menu">
                                <a href="{{ route('manage.evacuee.record', 'manage') }}" class="menu-link">
                                    <i class="bi bi-person-gear"></i>
                                    <span class="links_name">Manage Evacuee</span>
                                </a>
                                <a href="{{ route('manage.evacuee.record', 'archived') }}" class="menu-link">
                                    <i class="bi bi-person-slash"></i>
                                    <span class="links_name">Evacuee History</span>
                                </a>
                            </div>
                        </div>
                        <div class="navigation-item">
                            <a class="sub-btn">
                                <i class="bi bi-house"></i>
                                <span class="links_name">Evacuation Center</span>
                                <i class="bi bi-caret-right-fill dropdown"></i>
                            </a>
                            <div class="sub-menu">
                                <a href="{{ route('evacuation.center', 'active') }}" class="menu-link">
                                    <i class="bi bi-house-gear"></i>
                                    <span class="links_name">Manage Evacuation</span>
                                </a>
                                <a href="{{ route('evacuation.center', 'archived') }}" class="menu-link">
                                    <i class="bi bi-house-slash"></i>
                                    <span class="links_name">Archived Evacuation</span>
                                </a>
                            </div>
                        </div>
                        <div class="navigation-item">
                            <a href="{{ route('evacuation.center.locator') }}" class="menu-link">
                                <i class="bi bi-search"></i>
                                <span class="links_name">Evacuation Center Locator</span>
                            </a>
                        </div>
                        @if (auth()->user()->position == 'Focal')
                            <div class="navigation-item">
                                <a class="sub-btn">
                                    <i class="bi bi-person-vcard"></i>
                                    <span class="links_name">Users Account</span>
                                    <i class="bi bi-caret-right-fill dropdown"></i>
                                </a>
                                <div class="sub-menu">
                                    <a href="{{ route('display.users.account', 'active') }}" class="menu-link">
                                        <i class="bi bi-person-gear"></i>
                                        <span class="links_name">Manage Account</span>
                                    </a>
                                    <a href="{{ route('display.users.account', 'archived') }}" class="menu-link">
                                        <i class="bi bi-person-slash"></i>
                                        <span class="links_name">Archived Account</span>
                                    </a>
                                    <a href="{{ route('activity.log') }}" class="menu-link">
                                        <i class="bi bi-card-list"></i>
                                        <span class="links_name">User Activity Log</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                        <div class="navigation-item">
                            <a href="{{ route('hotline.number') }}" class="menu-link">
                                <i class="bi bi-telephone"></i>
                                <span class="links_name">Hotline Numbers</span>
                            </a>
                        </div>
                    @endif
                @endauth
                @guest
                    <div class="navigation-item">
                        <a href="{{ route('resident.eligtas.guideline') }}" class="menu-link">
                            <i class="bi bi-book"></i>
                            <span class="links_name">E-LIGTAS Guidelines</span>
                        </a>
                    </div>
                    <div class="navigation-item">
                        <a href="{{ route('resident.evacuation.center.locator') }}" class="menu-link">
                            <i class="bi bi-search"></i>
                            <span class="links_name">Evacuation Center Locator</span>
                        </a>
                    </div>
                    <div class="navigation-item">
                        <a href="{{ route('resident.reporting') }}" class="menu-link">
                            <i class="bi bi-megaphone"></i>
                            <span class="links_name">Report Incident</span>
                        </a>
                    </div>
                    <div class="navigation-item">
                        <a href="{{ route('resident.hotline.number') }}" class="menu-link">
                            <i class="bi bi-telephone"></i>
                            <span class="links_name">Hotline Numbers</span>
                        </a>
                    </div>
                    <div class="navigation-item">
                        <a href="{{ route('login') }}" id="loginLink">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span class="links_name">Login</span>
                        </a>
                    </div>
                @endguest
            </ul>
        </div>
    </div>
</aside>
