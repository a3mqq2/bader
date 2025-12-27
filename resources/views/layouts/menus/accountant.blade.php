<!-- الصفحة الرئيسية -->
<li class="pc-item {{ request()->routeIs('accountant.dashboard') ? 'active' : '' }}">
    <a href="{{ route('accountant.dashboard') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-dashboard f-22"></i>
        </span>
        <span class="pc-mtext">لوحة التحكم</span>
    </a>
</li>

<!-- قسم الإدارة المالية -->
<li class="pc-item pc-caption">
    <label>الإدارة المالية</label>
</li>

<!-- الخزائن والحركات -->
<li class="pc-item pc-hasmenu {{ request()->routeIs('accountant.finance.*') ? 'pc-trigger active' : '' }}">
    <a href="javascript:void(0)" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-building-bank f-22"></i>
        </span>
        <span class="pc-mtext">الخزائن والحركات</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item {{ request()->routeIs('accountant.finance.treasuries.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('accountant.finance.treasuries.index') }}">
                <i class="ti ti-safe me-2"></i>
                الخزائن المالية
            </a>
        </li>
        <li class="pc-item {{ request()->routeIs('accountant.finance.transactions.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('accountant.finance.transactions.index') }}">
                <i class="ti ti-arrows-exchange me-2"></i>
                الحركات المالية
            </a>
        </li>
        <li class="pc-item {{ request()->routeIs('accountant.finance.categories.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('accountant.finance.categories.index') }}">
                <i class="ti ti-tags me-2"></i>
                تصنيفات الحركات
            </a>
        </li>
    </ul>
</li>

<!-- قسم شؤون الموظفين -->
<li class="pc-item pc-caption">
    <label>شؤون الموظفين</label>
</li>

<!-- الرواتب والحسابات -->
<li class="pc-item pc-hasmenu {{ request()->routeIs('accountant.payroll.*') || request()->routeIs('accountant.employee-accounts.*') ? 'pc-trigger active' : '' }}">
    <a href="javascript:void(0)" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-users f-22"></i>
        </span>
        <span class="pc-mtext">الرواتب والحسابات</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item {{ request()->routeIs('accountant.payroll.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('accountant.payroll.show') }}">
                <i class="ti ti-report-money me-2"></i>
                كشف المرتبات
            </a>
        </li>
        <li class="pc-item {{ request()->routeIs('accountant.employee-accounts.*') ? 'active' : '' }}">
            <a class="pc-link" href="{{ route('accountant.employee-accounts.index') }}">
                <i class="ti ti-wallet me-2"></i>
                حسابات الموظفين
            </a>
        </li>
    </ul>
</li>

<!-- قسم التحصيل -->
<li class="pc-item pc-caption">
    <label>التحصيل والإيرادات</label>
</li>

<!-- قائمة المستحقات -->
<li class="pc-item {{ request()->routeIs('accountant.dues.*') ? 'active' : '' }}">
    <a href="{{ route('accountant.dues.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-receipt f-22"></i>
        </span>
        <span class="pc-mtext">مستحقات من الطلاب</span>
    </a>
</li>
