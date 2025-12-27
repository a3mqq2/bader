@extends('layouts.app')

@section('title', 'تقرير الطلاب')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- فلتر التاريخ -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="ti ti-filter me-2"></i>
                    فلترة التقرير
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديد</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="under_assessment" {{ request('status') == 'under_assessment' ? 'selected' : '' }}>تحت التقييم</option>
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
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.reports.students') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- التقرير -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="ti ti-users me-2"></i>
                    تقرير الطلاب
                    <span class="badge bg-primary ms-2">{{ $students->count() }}</span>
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.students', array_merge(request()->query(), ['print' => 1])) }}"
                       class="btn btn-outline-info btn-sm" target="_blank">
                        <i class="ti ti-printer me-1"></i> طباعة
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- إحصائيات سريعة -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="bg-light rounded p-3 text-center">
                            <h4 class="mb-1 text-primary">{{ $stats['total'] }}</h4>
                            <small class="text-muted">إجمالي الطلاب</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light rounded p-3 text-center">
                            <h4 class="mb-1 text-success">{{ $stats['active'] }}</h4>
                            <small class="text-muted">نشط</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light rounded p-3 text-center">
                            <h4 class="mb-1 text-info">{{ $stats['new'] }}</h4>
                            <small class="text-muted">جديد</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light rounded p-3 text-center">
                            <h4 class="mb-1 text-warning">{{ $stats['under_assessment'] }}</h4>
                            <small class="text-muted">تحت التقييم</small>
                        </div>
                    </div>
                </div>

                <!-- جدول البيانات -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>اسم الطالب</th>
                                <th>العمر</th>
                                <th>الجنس</th>
                                <th>ولي الأمر</th>
                                <th>الهاتف</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->age }}</td>
                                <td>{{ $student->gender_text }}</td>
                                <td>{{ $student->guardian_name }}</td>
                                <td>{{ $student->phone }}</td>
                                <td><span class="badge bg-{{ $student->status_color }}">{{ $student->status_text }}</span></td>
                                <td>{{ $student->created_at->format('Y/m/d') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="ti ti-users-minus fa-2x mb-2 d-block"></i>
                                    لا توجد بيانات
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
