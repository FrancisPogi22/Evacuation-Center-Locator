<aside class="sidebar">
    <div class="sidebar-header">
        <img id="logo" src="{{ asset('assets/img/e-ligtas-logo.png') }}" alt="Logo">
        <button type="button" class="bi bi-x" id="btn-sidebar-close"></button>
    </div>
    <div class="sidebar-content">
        <div class="user-details">
            <div class="details-content">
                @auth
                    @if (auth()->user()->organization == 'CDRRMO' || auth()->user()->organization == 'CSWD')
                        @if (auth()->user()->is_disable == 0)
                            <div title="Active" class="user-active"></div>
                        @else
                            <div title="Currently Disabled" class="user-disable"></div>
                        @endif
                        <img src="{{ asset('assets/img/' . auth()->user()->organization . '-LOGO.png') }}" alt="Logo">
                        <span>{{ auth()->user()->organization == 'CDRRMO' ? 'CDRRMO' : 'CSWDO' }} Panel</span>
                    @endif
                @endauth
                @guest
                    <div title="Resident" class="resident"></div>
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
                            <a href="{{ route('guideline.display') }}" class="menu-link">
                                <i class="bi bi-book"></i>
                                <span class="links_name">E-LIGTAS Guidelines</span>
                            </a>
                        </div>
                        <div class="navigation-item">
                            <a class="sub-btn">
                                <i class="bi bi-megaphone"></i>
                                <span class="links_name">Report Incident</span>
                                <i class="bi bi-caret-right-fill dropdown"></i>
                            </a>
                            <div class="sub-menu">
                                <a href="{{ route('incident.report', 'pending') }}" class="menu-link">
                                    <i class="bi bi-people"></i>
                                    <span class="links_name">Manage Reports</span>
                                </a>
                                <a href="{{ route('incident.report', 'report') }}" class="menu-link">
                                    <i class="bi bi-archive"></i>
                                    <span class="links_name">Archived Reports</span>
                                </a>
                            </div>
                        </div>
                        @if (auth()->user()->position == 'President')
                            <div class="navigation-item">
                                <a class="sub-btn">
                                    <i class="bi bi-people"></i>
                                    <span class="links_name">CDRRMO Account</span>
                                    <i class="bi bi-caret-right-fill dropdown"></i>
                                </a>
                                <div class="sub-menu">
                                    <a href="{{ route('display.users.account', 'active') }}" class="menu-link">
                                        <i class="bi bi-person-gear"></i>
                                        <span class="links_name">Manage Accounts</span>
                                    </a>
                                    <a href="" class="menu-link">
                                        <i class="bi bi-person-slash"></i>
                                        <span class="links_name">Archived Accounts</span>
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
                        <div class="navigation-item">
                            <a href="{{ route('about') }}" class="menu-link">
                                <i class="bi bi-info-circle"></i>
                                <span class="links_name">About</span>
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
                            <a href="{{ route('guideline.display') }}" class="menu-link">
                                <i class="bi bi-book"></i>
                                <span class="links_name">E-LIGTAS Guidelines</span>
                            </a>
                        </div>
                        <div class="navigation-item">
                            <a class="sub-btn">
                                <i class="bi bi-tropical-storm"></i>
                                <span class="links_name">Disaster Information</span>
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
                            <a href="{{ route('manage.evacuee.record') }}" class="menu-link">
                                <i class="bi bi-people"></i>
                                <span class="links_name">Manage Evacuee Information</span>
                            </a>
                        </div>
                        <div class="navigation-item">
                            <a class="sub-btn">
                                <i class="bi bi-hospital"></i>
                                <span class="links_name">Evacuation Centers</span>
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
                                    <i class="bi bi-people"></i>
                                    <span class="links_name">Manage Users Account</span>
                                    <i class="bi bi-caret-right-fill dropdown"></i>
                                </a>
                                <div class="sub-menu">
                                    <a href="{{ route('display.users.account', 'active') }}" class="menu-link">
                                        <i class="bi bi-person-gear"></i>
                                        <span class="links_name">Users Account</span>
                                    </a>
                                    <a href="{{ route('display.users.account', 'archived') }}" class="menu-link">
                                        <i class="bi bi-person-slash"></i>
                                        <span class="links_name">Archived Account</span>
                                    </a>
                                </div>
                            </div>
                            <div class="navigation-item">
                                <a href="{{ route('activity.log') }}" class="menu-link">
                                    <i class="bi bi-card-checklist"></i>
                                    <span class="links_name">User Activity Log</span>
                                </a>
                            </div>
                        @endif
                        <div class="navigation-item">
                            <a class="sub-btn">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span class="links_name">Report Danger Areas</span>
                                <i class="bi bi-caret-right-fill dropdown"></i>
                            </a>
                            <div class="sub-menu">
                                <a href="{{ route('danger.area.report', 'report') }}" class="menu-link">
                                    <i class="bi bi-flag"></i>
                                    <span class="links_name">Manage Reports</span>
                                </a>
                                <a href="{{ route('danger.area.report', 'archived') }}" class="menu-link">
                                    <i class="bi bi-bookmark-x"></i>
                                    <span class="links_name">Archived Reports</span>
                                </a>
                            </div>
                        </div>
                        <div class="navigation-item">
                            <a href="{{ route('hotline.number') }}" class="menu-link">
                                <i class="bi bi-telephone"></i>
                                <span class="links_name">Hotline Numbers</span>
                            </a>
                        </div>
                        <div class="navigation-item">
                            <a href="{{ route('about') }}" class="menu-link">
                                <i class="bi bi-info-circle"></i>
                                <span class="links_name">About</span>
                            </a>
                        </div>
                    @endif
                @endauth
                @guest
                    <div class="navigation-item">
                        <a href="{{ route('resident.guideline') }}" class="menu-link">
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
                        <a href="{{ route('resident.display.incident.report', 'pending') }}" class="menu-link">
                            <i class="bi bi-megaphone"></i>
                            <span class="links_name">Report Incident</span>
                        </a>
                    </div>
                    <div class="navigation-item">
                        <a href="{{ route('resident.danger.area.report', 'report') }}" class="menu-link">
                            <i class="bi bi-exclamation-triangle"></i>
                            <span class="links_name">Report Dangerous Areas</span>
                        </a>
                    </div>
                    <div class="navigation-item">
                        <a href="{{ route('resident.hotline.number') }}" class="menu-link">
                            <i class="bi bi-telephone"></i>
                            <span class="links_name">Hotline Numbers</span>
                        </a>
                    </div>
                    <div class="navigation-item">
                        <a href="{{ route('resident.about') }}" class="menu-link">
                            <i class="bi bi-info-circle"></i>
                            <span class="links_name">About</span>
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
