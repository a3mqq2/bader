<li class="pc-item pc-caption">
    <label>المشرف</label>
    <i class="ti ti-dashboard"></i>
</li>

<li class="pc-item {{ request()->routeIs('supervisor.attendance.index') ? 'active' : '' }}">
    <a href="{{ route('supervisor.attendance.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-scan"></i>
        </span>
        <span class="pc-mtext">تسجيل الحضور</span>
    </a>
</li>

<li class="pc-item {{ request()->routeIs('supervisor.attendance.log') ? 'active' : '' }}">
    <a href="{{ route('supervisor.attendance.log') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-list-check"></i>
        </span>
        <span class="pc-mtext">سجل الحضور</span>
    </a>
</li>
