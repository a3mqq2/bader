@extends('layouts.app')

@section('title', 'تقرير مؤشرات الخطر')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- التقرير -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="ti ti-alert-triangle me-2"></i>
                    تقرير مؤشرات الخطر
                </h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.risk', ['print' => 1]) }}"
                       class="btn btn-outline-info btn-sm" target="_blank">
                        <i class="ti ti-printer me-1"></i> طباعة
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- ملخص المؤشرات -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center p-3">
                                <h3 class="mb-1">{{ $stats['at_risk'] }}</h3>
                                <small>في خطر</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning">
                            <div class="card-body text-center p-3">
                                <h3 class="mb-1">{{ $stats['without_case'] }}</h3>
                                <small>بدون دراسة حالة</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center p-3">
                                <h3 class="mb-1">{{ $stats['under_assessment'] }}</h3>
                                <small>تحت التقييم</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center p-3">
                                <h3 class="mb-1">{{ $stats['assessment_delayed'] }}</h3>
                                <small>تقييم متأخر</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-dark text-white">
                            <div class="card-body text-center p-3">
                                <h3 class="mb-1">{{ $stats['absent_3_days'] }}</h3>
                                <small>غياب +3 أيام</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center p-3">
                                <h3 class="mb-1">{{ $stats['absent_week'] }}</h3>
                                <small>غياب +أسبوع</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- طلاب في خطر -->
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-urgent text-danger me-2"></i>
                    طلاب في خطر (غياب متتالي بدون إذن)
                    <span class="badge bg-danger">{{ $atRiskStudents->count() }}</span>
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-danger">
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>الطالب</th>
                                <th>أيام الغياب المتتالية</th>
                                <th>آخر حضور</th>
                                <th>الهاتف</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($atRiskStudents as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                <td class="fw-bold">{{ $student->name }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $student->consecutive_unexcused_absence_days }} يوم</span>
                                </td>
                                <td>{{ $student->last_attendance_date?->format('Y/m/d') ?? 'لم يحضر' }}</td>
                                <td>
                                    <a href="tel:{{ $student->phone }}" class="text-decoration-none">{{ $student->phone }}</a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    <i class="ti ti-check text-success me-1"></i>
                                    لا يوجد طلاب في خطر
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- طلاب بدون دراسة حالة -->
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-file-off text-warning me-2"></i>
                    طلاب بدون دراسة حالة
                    <span class="badge bg-warning">{{ $withoutCaseStudents->count() }}</span>
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-warning">
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>الطالب</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th>مدة الانتظار</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withoutCaseStudents as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                <td class="fw-bold">{{ $student->name }}</td>
                                <td><span class="badge bg-{{ $student->status_color }}">{{ $student->status_text }}</span></td>
                                <td>{{ $student->created_at->format('Y/m/d') }}</td>
                                <td>
                                    <span class="badge bg-{{ $student->created_at->diffInDays(now()) > 7 ? 'danger' : 'secondary' }}">
                                        {{ $student->created_at->diffInDays(now()) }} يوم
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.students.case.create', $student) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="ti ti-plus"></i> إنشاء
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    <i class="ti ti-check text-success me-1"></i>
                                    جميع الطلاب لديهم دراسة حالة
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- طلاب تحت التقييم -->
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-clipboard-check text-info me-2"></i>
                    طلاب تحت التقييم
                    <span class="badge bg-info">{{ $underAssessmentStudents->count() }}</span>
                </h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-info">
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>الطالب</th>
                                <th>تاريخ بدء التقييم</th>
                                <th>المدة</th>
                                <th>التقييمات المكتملة</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($underAssessmentStudents as $index => $student)
                            @php
                                $daysSinceAssessment = $student->updated_at->diffInDays(now());
                                $isDelayed = $daysSinceAssessment > 14;
                            @endphp
                            <tr class="{{ $isDelayed ? 'table-danger' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                <td class="fw-bold">
                                    {{ $student->name }}
                                    @if($isDelayed)
                                        <i class="ti ti-alert-triangle text-danger ms-1" title="تقييم متأخر"></i>
                                    @endif
                                </td>
                                <td>{{ $student->updated_at->format('Y/m/d') }}</td>
                                <td>
                                    <span class="badge bg-{{ $isDelayed ? 'danger' : 'secondary' }}">
                                        {{ $daysSinceAssessment }} يوم
                                    </span>
                                </td>
                                <td>{{ $student->currentCase?->invoice?->items?->where('result', '!=', null)->count() ?? 0 }}</td>
                                <td>
                                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-info">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    <i class="ti ti-check text-success me-1"></i>
                                    لا يوجد طلاب تحت التقييم
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- طلاب غائبون -->
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ti ti-calendar-off text-dark me-2"></i>
                    طلاب غائبون أكثر من 3 أيام
                    <span class="badge bg-dark">{{ $absentStudents->count() }}</span>
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>الكود</th>
                                <th>الطالب</th>
                                <th>أيام الغياب</th>
                                <th>آخر حضور</th>
                                <th>الهاتف</th>
                                <th>الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absentStudents as $index => $student)
                            <tr class="{{ $student->days_since_last_attendance >= 7 ? 'table-danger' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge bg-secondary">{{ $student->code }}</span></td>
                                <td class="fw-bold">{{ $student->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $student->days_since_last_attendance >= 7 ? 'danger' : 'warning' }}">
                                        {{ $student->days_since_last_attendance }} يوم
                                    </span>
                                </td>
                                <td>{{ $student->last_attendance_date?->format('Y/m/d') ?? 'لم يحضر' }}</td>
                                <td>
                                    <a href="tel:{{ $student->phone }}" class="text-decoration-none">{{ $student->phone }}</a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    <i class="ti ti-check text-success me-1"></i>
                                    لا يوجد طلاب غائبون
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
