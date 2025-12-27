<!-- الرئيسية -->
<li class="pc-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <a href="{{ route('admin.dashboard') }}" class="pc-link">
        <span class="pc-micon">
            <svg class="pc-icon">
                <use xlink:href="#custom-status-up"></use>
            </svg>
        </span>
        <span class="pc-mtext">الرئيسية</span>
    </a>
</li>

<!-- الطلاب -->
<li class="pc-item pc-hasmenu {{ request()->routeIs('admin.students.*') ? 'active pc-trigger' : '' }}">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <svg class="pc-icon">
                <use xlink:href="#custom-user-square"></use>
            </svg>
        </span>
        <span class="pc-mtext">الطلاب</span>
        <span class="pc-arrow"><i data-feather="chevron-left"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item {{ request()->routeIs('admin.students.index') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.students.index') }}">عرض الطلاب</a>
        </li>
        <li class="pc-item {{ request()->routeIs('admin.students.create') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.students.create') }}">إضافة طالب</a>
        </li>
    </ul>
</li>

<!-- المتابعة الإدارية -->
<li class="pc-item pc-hasmenu {{ request()->routeIs('admin.today-assessments') || request()->routeIs('admin.sessions.*') || request()->routeIs('admin.daycare.*') ? 'active pc-trigger' : '' }}">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <svg class="pc-icon">
                <use xlink:href="#custom-presentation-chart"></use>
            </svg>
        </span>
        <span class="pc-mtext">المتابعة الإدارية</span>
        <span class="pc-arrow"><i data-feather="chevron-left"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item {{ request()->routeIs('admin.today-assessments') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.today-assessments') }}">التقييمات</a>
        </li>
        <li class="pc-item {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.sessions.index') }}">الجلسات</a>
        </li>
        <li class="pc-item {{ request()->routeIs('admin.daycare.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.daycare.index') }}">الرعاية النهارية</a>
        </li>
    </ul>
</li>

<!-- المستخدمين -->
<li class="pc-item pc-hasmenu {{ request()->routeIs('admin.users.*') ? 'active pc-trigger' : '' }}">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <svg class="pc-icon">
                <use xlink:href="#custom-profile-2user-outline"></use>
            </svg>
        </span>
        <span class="pc-mtext">المستخدمين</span>
        <span class="pc-arrow"><i data-feather="chevron-left"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.users.index') }}">عرض المستخدمين</a>
        </li>
        <li class="pc-item {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.users.create') }}">إضافة مستخدم</a>
        </li>
    </ul>
</li>

<!-- الإدارة العامة -->
<li class="pc-item pc-hasmenu {{ request()->routeIs('admin.assessments.*') || request()->routeIs('admin.therapy-sessions.*') || request()->routeIs('admin.daycare-types.*') || request()->routeIs('admin.reports.*') || request()->routeIs('admin.activity-logs.*') || request()->routeIs('admin.settings.*') ? 'active pc-trigger' : '' }}">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <svg class="pc-icon">
                <use xlink:href="#custom-setting-outline"></use>
            </svg>
        </span>
        <span class="pc-mtext">الإدارة العامة</span>
        <span class="pc-arrow"><i data-feather="chevron-left"></i></span>
    </a>
    <ul class="pc-submenu">
        <!-- التسعيرات قائمة فرعية -->
        <li class="pc-item pc-hasmenu {{ request()->routeIs('admin.assessments.*') || request()->routeIs('admin.therapy-sessions.*') || request()->routeIs('admin.daycare-types.*') ? 'active pc-trigger' : '' }}">
            <a href="#!" class="pc-link">التسعيرات<span class="pc-arrow"><i data-feather="chevron-left"></i></span></a>
            <ul class="pc-submenu">
                <li class="pc-item {{ request()->routeIs('admin.assessments.*') ? 'active' : '' }}">
                    <a class="pc-link" href="{{ route('admin.assessments.index') }}">المقاييس</a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.therapy-sessions.*') ? 'active' : '' }}">
                    <a class="pc-link" href="{{ route('admin.therapy-sessions.index') }}">أنواع الجلسات</a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.daycare-types.*') ? 'active' : '' }}">
                    <a class="pc-link" href="{{ route('admin.daycare-types.index') }}">أنواع الرعاية</a>
                </li>
            </ul>
        </li>
        <li class="pc-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.reports.index') }}">مركز التقارير</a>
        </li>
        <li class="pc-item {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.activity-logs.index') }}">سجل النظام</a>
        </li>
        <li class="pc-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('admin.settings.index') }}">إعدادات النظام</a>
        </li>
    </ul>
</li>
