@extends('layouts.app')

@section('title', 'سجل النظام')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-history me-2" style="color: #063973;"></i>
                            سجل النظام
                        </h5>
                        <small class="text-muted">جميع العمليات والأنشطة في النظام</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">الموظف</label>
                            <select name="user_id" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">من تاريخ</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">نوع العملية</label>
                            <select name="action" class="form-select form-select-sm">
                                <option value="">الكل</option>
                                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>إنشاء</option>
                                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>تحديث</option>
                                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>حذف</option>
                                <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>تسجيل دخول</option>
                                <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>تسجيل خروج</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">بحث</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="ابحث في الوصف..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request()->hasAny(['user_id', 'date_from', 'date_to', 'action', 'search']))
                                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-body p-0">
                @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">الموظف</th>
                                <th>الوصف</th>
                                <th width="10%" class="text-center">العملية</th>
                                <th width="18%">التاريخ والوقت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $index => $log)
                            <tr>
                                <td class="text-muted">{{ $logs->firstItem() + $index }}</td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2 bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <span class="text-primary fw-bold" style="font-size: 0.75rem;">
                                                    {{ mb_substr($log->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <span>{{ $log->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">النظام</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $log->description }}
                                    @if($log->model_name)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-link me-1"></i>{{ $log->model_name }} #{{ $log->model_id }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $actionConfig = [
                                            'create' => ['label' => 'إنشاء', 'color' => 'success', 'icon' => 'plus'],
                                            'update' => ['label' => 'تحديث', 'color' => 'info', 'icon' => 'edit'],
                                            'delete' => ['label' => 'حذف', 'color' => 'danger', 'icon' => 'trash'],
                                            'login' => ['label' => 'دخول', 'color' => 'primary', 'icon' => 'sign-in-alt'],
                                            'logout' => ['label' => 'خروج', 'color' => 'secondary', 'icon' => 'sign-out-alt'],
                                            'payment' => ['label' => 'دفع', 'color' => 'success', 'icon' => 'money-bill'],
                                        ];
                                        $config = $actionConfig[$log->action] ?? ['label' => $log->action ?? '-', 'color' => 'secondary', 'icon' => 'circle'];
                                    @endphp
                                    <span class="badge bg-{{ $config['color'] }}">
                                        <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                        {{ $config['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-muted me-2"></i>
                                        <div>
                                            <span>{{ $log->created_at->format('Y/m/d') }}</span>
                                            <small class="text-muted d-block">{{ $log->created_at->format('h:i A') }}</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                <div class="card-footer">
                    {{ $logs->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-history fa-4x mb-3 opacity-25"></i>
                    <p class="mb-0">لا يوجد سجلات</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
