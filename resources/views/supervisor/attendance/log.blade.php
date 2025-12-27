@extends('layouts.app')

@section('title', 'سجل الحضور')

@section('content')
<div class="row">
    <!-- إحصائيات اليوم -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $todayStats['total'] }}</h3>
                                <p class="mb-0">إجمالي الحضور اليوم</p>
                            </div>
                            <i class="ti ti-users" style="font-size: 40px; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $todayStats['present'] }}</h3>
                                <p class="mb-0">حاضرين</p>
                            </div>
                            <i class="ti ti-user-check" style="font-size: 40px; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $todayStats['checked_out'] }}</h3>
                                <p class="mb-0">سجلوا خروج</p>
                            </div>
                            <i class="ti ti-logout" style="font-size: 40px; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $todayStats['still_in'] }}</h3>
                                <p class="mb-0">مازالوا في العمل</p>
                            </div>
                            <i class="ti ti-clock" style="font-size: 40px; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-list-check me-2"></i>
                        سجل الحضور
                    </h5>
                    <div>
                        <a href="{{ route('supervisor.attendance.index') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-scan me-1"></i>
                            تسجيل حضور
                        </a>
                        <a href="{{ route('supervisor.attendance.print', request()->query()) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                            <i class="ti ti-printer me-1"></i>
                            طباعة
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- فلاتر البحث -->
                <form method="GET" action="{{ route('supervisor.attendance.log') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">تاريخ محدد</label>
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">الموظف</label>
                            <select name="user_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">الكل</option>
                                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>حاضر</option>
                                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>متأخر</option>
                                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>غائب</option>
                                <option value="early_leave" {{ request('status') == 'early_leave' ? 'selected' : '' }}>خروج مبكر</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-secondary">
                                <i class="ti ti-search me-1"></i>
                                بحث
                            </button>
                            <a href="{{ route('supervisor.attendance.log') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- جدول السجل -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>الموظف</th>
                                <th>الكود</th>
                                <th>الدور</th>
                                <th>وقت الدخول</th>
                                <th>وقت الخروج</th>
                                <th>ساعات العمل</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ $loop->iteration + ($attendances->currentPage() - 1) * $attendances->perPage() }}</td>
                                <td>{{ $attendance->date->format('Y/m/d') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:11px;">
                                            {{ mb_substr($attendance->user->name, 0, 2) }}
                                        </div>
                                        <strong>{{ $attendance->user->name }}</strong>
                                    </div>
                                </td>
                                <td><code>{{ $attendance->user->code }}</code></td>
                                <td><span class="badge bg-info">{{ $attendance->user->role_text }}</span></td>
                                <td>
                                    @if($attendance->check_in)
                                        <span class="text-success">
                                            <i class="ti ti-login me-1"></i>
                                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->check_out)
                                        <span class="text-danger">
                                            <i class="ti ti-logout me-1"></i>
                                            {{ \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->work_hours)
                                        <strong>{{ $attendance->formatted_work_hours }}</strong>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $attendance->status_color }}">
                                        {{ $attendance->status_text }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="ti ti-clipboard-x" style="font-size: 48px; color: #ccc;"></i>
                                    <p class="text-muted mt-2 mb-0">لا توجد سجلات</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($attendances->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            عرض {{ $attendances->firstItem() }} إلى {{ $attendances->lastItem() }} من {{ $attendances->total() }} سجل
                        </div>
                        <div>
                            {{ $attendances->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
