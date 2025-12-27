@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="row g-3">

    {{-- ملخص سريع --}}
    <div class="col-12">
        <div class="card">
            <div class="card-body py-3">
                <div class="row text-center">
                    <div class="col-md-3 col-6 border-end">
                        <h4 class="text-primary mb-1">{{ $sessionStats['today'] }}</h4>
                        <small class="text-muted">جلسات اليوم</small>
                    </div>
                    <div class="col-md-3 col-6 border-end">
                        <h4 class="text-success mb-1">{{ $daycareStats['present_today'] }}</h4>
                        <small class="text-muted">حضور الرعاية</small>
                    </div>
                    <div class="col-md-3 col-6 border-end">
                        <h4 class="text-info mb-1">{{ $todayAssessmentsCount }}</h4>
                        <small class="text-muted">تقييمات اليوم</small>
                    </div>
                    <div class="col-md-3 col-6">
                        <h4 class="text-warning mb-1">{{ $employeeStats['present_today'] }}/{{ $employeeStats['total'] }}</h4>
                        <small class="text-muted">حضور الموظفين</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- إحصائيات الطلاب --}}
    <div class="col-12">
        <h6 class="text-muted fw-bold mb-3">
            <i class="ti ti-users me-2"></i>إحصائيات الطلاب
        </h6>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.students.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-xl bg-light-primary rounded-3">
                                <i class="ti ti-users fs-1 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 me-3">
                            <h2 class="mb-1">{{ $studentStats['total'] }}</h2>
                            <p class="text-muted mb-0">إجمالي الطلاب</p>
                        </div>
                    </div>
                    @if($studentStats['new_this_week'] > 0)
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-success">
                            <i class="ti ti-trending-up me-1"></i>
                            +{{ $studentStats['new_this_week'] }} هذا الأسبوع
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.students.index', ['status' => 'under_assessment']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-xl bg-light-warning rounded-3">
                                <i class="ti ti-clipboard-check fs-1 text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 me-3">
                            <h2 class="mb-1">{{ $studentStats['under_assessment'] }}</h2>
                            <p class="text-muted mb-0">قيد التقييم</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.students.index', ['status' => 'active']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-xl bg-light-success rounded-3">
                                <i class="ti ti-user-check fs-1 text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 me-3">
                            <h2 class="mb-1">{{ $studentStats['active'] }}</h2>
                            <p class="text-muted mb-0">طلاب نشطين</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.at-risk-students') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-xl bg-light-danger rounded-3">
                                <i class="ti ti-alert-triangle fs-1 text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 me-3">
                            <h2 class="mb-1">{{ $studentAbsenceStats['unexcused'] }}</h2>
                            <p class="text-muted mb-0">غياب بدون إذن</p>
                        </div>
                    </div>
                    @if($studentAbsenceStats['unexcused'] > 0)
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-danger">
                            <i class="ti ti-alert-circle me-1"></i>
                            يتطلب متابعة
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </a>
    </div>

    {{-- بطاقات ملونة للإحصائيات اليومية --}}
    <div class="col-12 mt-4">
        <h6 class="text-muted fw-bold mb-3">
            <i class="ti ti-calendar-event me-2"></i>نشاط اليوم
        </h6>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.today-assessments') }}" class="text-decoration-none">
            <div class="card bg-primary border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white opacity-75 mb-1">تقييمات اليوم</p>
                            <h1 class="text-white mb-0">{{ $todayAssessmentsCount }}</h1>
                        </div>
                        <div class="avtar avtar-xl bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-clipboard-list fs-1 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.sessions.today') }}" class="text-decoration-none">
            <div class="card bg-success border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white opacity-75 mb-1">جلسات اليوم</p>
                            <h1 class="text-white mb-0">{{ $sessionStats['today'] }}</h1>
                            <span class="text-white opacity-75">{{ $sessionStats['completed_today'] }} مكتملة</span>
                        </div>
                        <div class="avtar avtar-xl bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-stethoscope fs-1 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.daycare.index') }}" class="text-decoration-none">
            <div class="card bg-info border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white opacity-75 mb-1">الرعاية النهارية</p>
                            <h1 class="text-white mb-0">{{ $daycareStats['active_students'] }}</h1>
                            <span class="text-white opacity-75">{{ $daycareStats['present_today'] }} حاضر</span>
                        </div>
                        <div class="avtar avtar-xl bg-white bg-opacity-25 rounded-3">
                            <i class="ti ti-heart-handshake fs-1 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
            <div class="card bg-warning border-0 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-dark opacity-75 mb-1">حضور الموظفين</p>
                            <h1 class="text-dark mb-0">{{ $employeeStats['present_today'] }}/{{ $employeeStats['total'] }}</h1>
                            <span class="text-dark opacity-75">{{ $employeeStats['absent_today'] }} غائب</span>
                        </div>
                        <div class="avtar avtar-xl bg-dark bg-opacity-10 rounded-3">
                            <i class="ti ti-id-badge-2 fs-1 text-dark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- الرسم البياني والتنبيهات --}}
    <div class="col-lg-8 mt-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-chart-line me-2"></i>نشاط الأسبوع
                </h5>
            </div>
            <div class="card-body">
                <div id="weeklyChart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mt-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-bell me-2"></i>التنبيهات
                </h5>
                @if(count($alerts) > 0)
                <span class="badge bg-danger rounded-pill">{{ count($alerts) }}</span>
                @endif
            </div>
            <div class="card-body p-0" style="max-height: 340px; overflow-y: auto;">
                @forelse($alerts as $alert)
                <a href="{{ $alert['link'] }}" class="d-flex align-items-center gap-3 p-3 border-bottom text-decoration-none">
                    <div class="avtar avtar-s bg-light-{{ $alert['type'] }} rounded">
                        <i class="{{ $alert['icon'] }} text-{{ $alert['type'] }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 text-dark small">{{ $alert['message'] }}</p>
                    </div>
                    <i class="ti ti-chevron-left text-muted"></i>
                </a>
                @empty
                <div class="text-center py-5">
                    <i class="ti ti-circle-check fs-1 text-success mb-2"></i>
                    <p class="text-muted mb-0">لا توجد تنبيهات</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- الوصول السريع --}}
    <div class="col-12 mt-4">
        <h6 class="text-muted fw-bold mb-3">
            <i class="ti ti-rocket me-2"></i>الوصول السريع
        </h6>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <a href="{{ route('admin.students.create') }}" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <div class="avtar avtar-xl bg-light-primary rounded-3 mx-auto mb-3">
                    <i class="ti ti-user-plus fs-1 text-primary"></i>
                </div>
                <p class="mb-0 text-dark fw-medium">إضافة طالب</p>
            </div>
        </a>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <a href="{{ route('admin.sessions.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <div class="avtar avtar-xl bg-light-success rounded-3 mx-auto mb-3">
                    <i class="ti ti-calendar fs-1 text-success"></i>
                </div>
                <p class="mb-0 text-dark fw-medium">إدارة الجلسات</p>
            </div>
        </a>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <a href="{{ route('admin.today-assessments') }}" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <div class="avtar avtar-xl bg-light-warning rounded-3 mx-auto mb-3">
                    <i class="ti ti-clipboard-check fs-1 text-warning"></i>
                </div>
                <p class="mb-0 text-dark fw-medium">التقييمات</p>
            </div>
        </a>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <a href="{{ route('admin.daycare.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <div class="avtar avtar-xl bg-light-info rounded-3 mx-auto mb-3">
                    <i class="ti ti-heart-handshake fs-1 text-info"></i>
                </div>
                <p class="mb-0 text-dark fw-medium">الرعاية النهارية</p>
            </div>
        </a>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <a href="{{ route('admin.reports.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <div class="avtar avtar-xl bg-light-secondary rounded-3 mx-auto mb-3">
                    <i class="ti ti-chart-pie fs-1 text-secondary"></i>
                </div>
                <p class="mb-0 text-dark fw-medium">التقارير</p>
            </div>
        </a>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <a href="{{ route('admin.users.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body text-center py-4">
                <div class="avtar avtar-xl bg-light-danger rounded-3 mx-auto mb-3">
                    <i class="ti ti-users fs-1 text-danger"></i>
                </div>
                <p class="mb-0 text-dark fw-medium">المستخدمين</p>
            </div>
        </a>
    </div>

    {{-- آخر النشاطات وحالة الجلسات --}}
    <div class="col-lg-6 mt-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-activity me-2"></i>آخر النشاطات
                </h5>
            </div>
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                @forelse($recentActivities as $activity)
                <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                    <div class="avtar avtar-s bg-light-primary rounded">
                        {{ mb_substr($activity->user->name ?? 'ن', 0, 1) }}
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-1 small">{{ $activity->description }}</p>
                        <small class="text-muted">
                            {{ $activity->user->name ?? 'النظام' }} • {{ $activity->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <p class="mb-0">لا توجد نشاطات</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6 mt-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-chart-donut me-2"></i>حالة جلسات الأسبوع
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-6">
                        <div id="sessionStatusChart" style="height: 180px;"></div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">مكتملة</span>
                                <span class="fw-bold text-success">{{ $sessionStats['completed_week'] }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $sessionStats['this_week'] > 0 ? ($sessionStats['completed_week'] / $sessionStats['this_week']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">غياب</span>
                                <span class="fw-bold text-danger">{{ $sessionStats['absent_week'] }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: {{ $sessionStats['this_week'] > 0 ? ($sessionStats['absent_week'] / $sessionStats['this_week']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">ملغية/مؤجلة</span>
                                <span class="fw-bold text-warning">{{ $sessionStats['cancelled_week'] }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: {{ $sessionStats['this_week'] > 0 ? ($sessionStats['cancelled_week'] / $sessionStats['this_week']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">إجمالي الأسبوع</span>
                            <span class="fw-bold text-primary">{{ $sessionStats['this_week'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // رسم بياني لنشاط الأسبوع
    var weeklyOptions = {
        series: [{
            name: 'الجلسات المكتملة',
            data: @json($chartData['sessions'])
        }, {
            name: 'حضور الرعاية',
            data: @json($chartData['daycare'])
        }],
        chart: {
            type: 'area',
            height: 300,
            fontFamily: 'Cairo, sans-serif',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: ['#198754', '#0dcaf0'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1
            }
        },
        xaxis: {
            categories: @json($chartData['labels'])
        },
        yaxis: {
            min: 0,
            forceNiceScale: true
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center'
        },
        tooltip: {
            shared: true,
            intersect: false
        }
    };
    new ApexCharts(document.querySelector("#weeklyChart"), weeklyOptions).render();

    // رسم بياني دائري لحالة الجلسات
    var statusOptions = {
        series: [{{ $sessionStats['completed_week'] }}, {{ $sessionStats['absent_week'] }}, {{ $sessionStats['cancelled_week'] }}],
        chart: {
            type: 'donut',
            height: 180,
            fontFamily: 'Cairo, sans-serif'
        },
        colors: ['#198754', '#dc3545', '#ffc107'],
        labels: ['مكتملة', 'غياب', 'ملغية'],
        legend: { show: false },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'الإجمالي',
                            fontSize: '14px',
                            fontWeight: 600
                        }
                    }
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#sessionStatusChart"), statusOptions).render();
});
</script>
@endpush
