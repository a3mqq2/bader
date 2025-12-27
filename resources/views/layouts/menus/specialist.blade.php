<!-- الصفحة الرئيسية -->
<li class="pc-item {{ request()->routeIs('specialist.dashboard') ? 'active' : '' }}">
    <a href="{{ route('specialist.dashboard') }}" class="pc-link">
        <span class="pc-micon">
            <svg class="pc-icon">
                <use xlink:href="#custom-status-up"></use>
            </svg>
        </span>
        <span class="pc-mtext">الرئيسية</span>
    </a>
</li>

<!-- جلساتي -->
<li class="pc-item {{ request()->routeIs('specialist.sessions.*') ? 'active' : '' }}">
    <a href="{{ route('specialist.sessions.index') }}" class="pc-link">
        <span class="pc-micon">
            <svg class="pc-icon">
                <use xlink:href="#custom-calendar-1"></use>
            </svg>
        </span>
        <span class="pc-mtext">جلساتي</span>
    </a>
</li>

<!-- الرعاية النهارية -->
<li class="pc-item {{ request()->routeIs('specialist.daycare.*') ? 'active' : '' }}">
    <a href="{{ route('specialist.daycare.index') }}" class="pc-link">
        <span class="pc-micon">
            <svg class="pc-icon">
                <use xlink:href="#custom-sun-1"></use>
            </svg>
        </span>
        <span class="pc-mtext">الرعاية النهارية</span>
    </a>
</li>
