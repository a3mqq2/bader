@extends('layouts.app')

@section('title', 'الطلاب')

@php
    // Helper function for sortable column headers
    $sortUrl = function($column) {
        $currentSort = request('sort_by', 'created_at');
        $currentDir = request('sort_dir', 'desc');

        $newDir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';

        return request()->fullUrlWithQuery([
            'sort_by' => $column,
            'sort_dir' => $newDir,
            'page' => 1 // Reset to first page when sorting
        ]);
    };

    $sortIcon = function($column) {
        $currentSort = request('sort_by', 'created_at');
        $currentDir = request('sort_dir', 'desc');

        if ($currentSort !== $column) {
            return '<i class="ti ti-arrows-sort text-muted ms-1"></i>';
        }
        return $currentDir === 'asc'
            ? '<i class="ti ti-sort-ascending text-primary ms-1"></i>'
            : '<i class="ti ti-sort-descending text-primary ms-1"></i>';
    };
@endphp

@push('styles')
<style>
    .table thead th a {
        cursor: pointer;
        white-space: nowrap;
    }
    .table thead th a:hover {
        color: #063973 !important;
    }
    .table thead th a i {
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- فلترة متقدمة -->
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-1"></i>
                        البحث والفلترة
                    </h6>
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-chevron-down me-1"></i>
                        عرض الفلاتر
                    </button>
                </div>
            </div>
            <div class="card-body collapse" id="filterCollapse">
                <!-- مؤشرات الخطر - فلترة سريعة -->
                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted me-2"><i class="ti ti-alert-triangle me-1"></i>مؤشرات الخطر:</span>

                        {{-- بدون دراسة حالة --}}
                        <a href="{{ route('admin.students.index', ['risk_filter' => 'without_case']) }}"
                           class="btn btn-sm {{ request('risk_filter') == 'without_case' ? 'btn-danger' : 'btn-outline-danger' }}">
                            <i class="ti ti-file-off me-1"></i>
                            بدون دراسة حالة
                            @if($riskStats['without_case'] > 0)
                                <span class="badge bg-white text-danger ms-1">{{ $riskStats['without_case'] }}</span>
                            @endif
                        </a>

                        {{-- تحت التقييم --}}
                        <a href="{{ route('admin.students.index', ['risk_filter' => 'under_assessment']) }}"
                           class="btn btn-sm {{ request('risk_filter') == 'under_assessment' ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="ti ti-clipboard-check me-1"></i>
                            تحت التقييم
                            @if($riskStats['under_assessment'] > 0)
                                <span class="badge bg-white text-info ms-1">{{ $riskStats['under_assessment'] }}</span>
                            @endif
                        </a>

                        {{-- تقييم متأخر --}}
                        <a href="{{ route('admin.students.index', ['risk_filter' => 'assessment_delayed']) }}"
                           class="btn btn-sm {{ request('risk_filter') == 'assessment_delayed' ? 'btn-warning' : 'btn-outline-warning' }}">
                            <i class="ti ti-clock-pause me-1"></i>
                            تقييم متأخر (+14 يوم)
                            @if($riskStats['assessment_delayed'] > 0)
                                <span class="badge bg-white text-warning ms-1">{{ $riskStats['assessment_delayed'] }}</span>
                            @endif
                        </a>

                        <span class="border-start mx-2" style="height: 24px;"></span>

                        {{-- غياب أكثر من 3 أيام --}}
                        <a href="{{ route('admin.students.index', ['risk_filter' => 'absent_3_days']) }}"
                           class="btn btn-sm {{ request('risk_filter') == 'absent_3_days' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                            <i class="ti ti-calendar-off me-1"></i>
                            غياب +3 أيام
                            @if($riskStats['absent_3_days'] > 0)
                                <span class="badge bg-danger text-white ms-1">{{ $riskStats['absent_3_days'] }}</span>
                            @endif
                        </a>

                        {{-- غياب أكثر من أسبوع --}}
                        <a href="{{ route('admin.students.index', ['risk_filter' => 'absent_week']) }}"
                           class="btn btn-sm {{ request('risk_filter') == 'absent_week' ? 'btn-danger' : 'btn-outline-danger' }}">
                            <i class="ti ti-calendar-x me-1"></i>
                            غياب +أسبوع
                            @if($riskStats['absent_week'] > 0)
                                <span class="badge bg-white text-danger ms-1">{{ $riskStats['absent_week'] }}</span>
                            @endif
                        </a>

                        {{-- طلاب في خطر --}}
                        <a href="{{ route('admin.students.index', ['risk_filter' => 'at_risk']) }}"
                           class="btn btn-sm {{ request('risk_filter') == 'at_risk' ? 'btn-danger' : 'btn-outline-danger' }} fw-bold">
                            <i class="ti ti-urgent me-1"></i>
                            في خطر
                            @if($riskStats['at_risk'] > 0)
                                <span class="badge bg-white text-danger ms-1">{{ $riskStats['at_risk'] }}</span>
                            @endif
                        </a>

                        @if(request('risk_filter'))
                            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-light">
                                <i class="ti ti-x me-1"></i> إلغاء الفلتر
                            </a>
                        @endif
                    </div>
                </div>

                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">بحث</label>
                            <input type="text" name="search" class="form-control" placeholder="الاسم، الكود، ولي الأمر، الهاتف..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">الكل</option>
                                <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديد</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">الجنس</label>
                            <select name="gender" class="form-select">
                                <option value="">الكل</option>
                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> بحث
                                </button>
                                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-1"></i> إعادة تعيين
                                </a>
                                <a href="{{ route('admin.students.print', request()->query()) }}" class="btn btn-outline-info" target="_blank">
                                    <i class="fas fa-print me-1"></i> طباعة
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- قائمة الطلاب -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-user-graduate me-1"></i>
                        قائمة الطلاب
                        <span class="badge bg-secondary ms-1">{{ $students->total() }}</span>
                    </h6>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> إضافة طالب
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th width="8%">
                                    <a href="{{ $sortUrl('code') }}" class="text-dark text-decoration-none d-flex align-items-center">
                                        الكود {!! $sortIcon('code') !!}
                                    </a>
                                </th>
                                <th width="18%">
                                    <a href="{{ $sortUrl('name') }}" class="text-dark text-decoration-none d-flex align-items-center">
                                        الطالب {!! $sortIcon('name') !!}
                                    </a>
                                </th>
                                <th width="8%">
                                    <a href="{{ $sortUrl('birth_date') }}" class="text-dark text-decoration-none d-flex align-items-center">
                                        العمر {!! $sortIcon('birth_date') !!}
                                    </a>
                                </th>
                                <th width="8%">
                                    <a href="{{ $sortUrl('gender') }}" class="text-dark text-decoration-none d-flex align-items-center">
                                        الجنس {!! $sortIcon('gender') !!}
                                    </a>
                                </th>
                                <th width="15%">
                                    <a href="{{ $sortUrl('guardian_name') }}" class="text-dark text-decoration-none d-flex align-items-center">
                                        ولي الأمر {!! $sortIcon('guardian_name') !!}
                                    </a>
                                </th>
                                <th width="12%">
                                    <a href="{{ $sortUrl('phone') }}" class="text-dark text-decoration-none d-flex align-items-center">
                                        الهاتف {!! $sortIcon('phone') !!}
                                    </a>
                                </th>
                                <th width="8%">
                                    <a href="{{ $sortUrl('status') }}" class="text-dark text-decoration-none d-flex align-items-center">
                                        الحالة {!! $sortIcon('status') !!}
                                    </a>
                                </th>
                                <th width="10%">
                                    <a href="{{ $sortUrl('created_at') }}" class="text-dark text-decoration-none d-flex align-items-center">
                                        تاريخ التسجيل {!! $sortIcon('created_at') !!}
                                    </a>
                                </th>
                                <th width="13%" class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-{{ $student->gender === 'male' ? 'info' : 'pink' }} text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px;height:35px;font-size:12px;{{ $student->gender === 'female' ? 'background-color:#e91e63!important;' : '' }}">
                                                <i class="fas fa-{{ $student->gender === 'male' ? 'mars' : 'venus' }}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">
                                                    {{ $student->name }}
                                                    @if($student->is_at_risk)
                                                    <i class="fas fa-exclamation-triangle text-danger ms-1" title="غياب متتالي {{ $student->consecutive_unexcused_absence_days }} يوم بدون إذن" data-bs-toggle="tooltip"></i>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $student->birth_date->format('Y/m/d') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->age }}</td>
                                    <td>{{ $student->gender_text }}</td>
                                    <td>{{ $student->guardian_name }}</td>
                                    <td>
                                        <a href="tel:{{ $student->phone }}" class="text-decoration-none">{{ $student->phone }}</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $student->status_color }}">{{ $student->status_text }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $student->created_at->format('Y/m/d') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye me-1"></i> عرض
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit me-1"></i> تعديل
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $student->id }}">
                                                <i class="fas fa-trash me-1"></i> حذف
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $student->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>تأكيد الحذف</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center py-4">
                                                        <i class="fas fa-user-graduate fa-3x text-danger mb-3"></i>
                                                        <p class="mb-1">هل أنت متأكد من حذف الطالب:</p>
                                                        <h5 class="text-primary">{{ $student->name }}</h5>
                                                        <small class="text-muted">{{ $student->code }}</small>
                                                    </div>
                                                    <div class="modal-footer justify-content-center">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-1"></i>إلغاء
                                                        </button>
                                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash me-1"></i>حذف
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-user-graduate fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">لا يوجد طلاب</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($students->hasPages())
                <div class="card-footer">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
