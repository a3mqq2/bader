@extends('layouts.app')

@section('title', 'مركز التقارير')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="students-tab" data-bs-toggle="tab" href="#students" role="tab">
                            <i class="ti ti-users me-1"></i> الطلاب
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="cases-tab" data-bs-toggle="tab" href="#cases" role="tab">
                            <i class="ti ti-clipboard-check me-1"></i> دراسة الحالة
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="sessions-tab" data-bs-toggle="tab" href="#sessions" role="tab">
                            <i class="ti ti-clock me-1"></i> الجلسات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="daycare-tab" data-bs-toggle="tab" href="#daycare" role="tab">
                            <i class="ti ti-home-heart me-1"></i> الرعاية النهارية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="invoices-tab" data-bs-toggle="tab" href="#invoices" role="tab">
                            <i class="ti ti-file-invoice me-1"></i> الفواتير
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="specialists-tab" data-bs-toggle="tab" href="#specialists" role="tab">
                            <i class="ti ti-user-check me-1"></i> الأخصائيين
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="risk-tab" data-bs-toggle="tab" href="#risk" role="tab">
                            <i class="ti ti-alert-triangle me-1"></i> مؤشرات الخطر
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="reportTabsContent">
                    <!-- تقرير الطلاب -->
                    <div class="tab-pane fade show active" id="students" role="tabpanel">
                        <form method="GET" action="{{ route('admin.reports.students') }}" target="_blank">
                            <div class="row g-3 mb-3">
                                <div class="col-md-2">
                                    <label class="form-label">من تاريخ</label>
                                    <input type="date" name="date_from" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">إلى تاريخ</label>
                                    <input type="date" name="date_to" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">الحالة</label>
                                    <select name="status" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="new">جديد</option>
                                        <option value="active">نشط</option>
                                        <option value="under_assessment">تحت التقييم</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">الجنس</label>
                                    <select name="gender" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="male">ذكر</option>
                                        <option value="female">أنثى</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-printer me-1"></i> عرض وطباعة التقرير
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- تقرير دراسة الحالة -->
                    <div class="tab-pane fade" id="cases" role="tabpanel">
                        <form method="GET" action="{{ route('admin.reports.cases') }}" target="_blank">
                            <div class="row g-3 mb-3">
                                <div class="col-md-2">
                                    <label class="form-label">من تاريخ</label>
                                    <input type="date" name="date_from" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">إلى تاريخ</label>
                                    <input type="date" name="date_to" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">حالة الدراسة</label>
                                    <select name="status" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="pending">قيد الانتظار</option>
                                        <option value="in_progress">جاري</option>
                                        <option value="completed">مكتمل</option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-printer me-1"></i> عرض وطباعة التقرير
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- تقرير الجلسات -->
                    <div class="tab-pane fade" id="sessions" role="tabpanel">
                        <form method="GET" action="{{ route('admin.reports.sessions') }}" target="_blank">
                            <div class="row g-3 mb-3">
                                <div class="col-md-2">
                                    <label class="form-label">من تاريخ</label>
                                    <input type="date" name="date_from" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">إلى تاريخ</label>
                                    <input type="date" name="date_to" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">حالة الجلسة</label>
                                    <select name="status" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="scheduled">مجدولة</option>
                                        <option value="completed">مكتملة</option>
                                        <option value="cancelled">ملغية</option>
                                        <option value="absent">غياب</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">نوع الجلسة</label>
                                    <select name="therapy_session_id" class="form-select">
                                        <option value="">الكل</option>
                                        @foreach(\App\Models\TherapySession::where('is_active', true)->get() as $session)
                                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-printer me-1"></i> عرض وطباعة التقرير
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- تقرير الرعاية النهارية -->
                    <div class="tab-pane fade" id="daycare" role="tabpanel">
                        <form method="GET" action="{{ route('admin.reports.daycare') }}" target="_blank">
                            <div class="row g-3 mb-3">
                                <div class="col-md-2">
                                    <label class="form-label">من تاريخ</label>
                                    <input type="date" name="date_from" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">إلى تاريخ</label>
                                    <input type="date" name="date_to" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">نوع الرعاية</label>
                                    <select name="daycare_type_id" class="form-select">
                                        <option value="">الكل</option>
                                        @foreach(\App\Models\DaycareType::where('is_active', true)->get() as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-printer me-1"></i> عرض وطباعة التقرير
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- تقرير الفواتير -->
                    <div class="tab-pane fade" id="invoices" role="tabpanel">
                        <form method="GET" action="{{ route('admin.reports.invoices') }}" target="_blank">
                            <div class="row g-3 mb-3">
                                <div class="col-md-2">
                                    <label class="form-label">من تاريخ</label>
                                    <input type="date" name="date_from" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">إلى تاريخ</label>
                                    <input type="date" name="date_to" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">حالة الفاتورة</label>
                                    <select name="status" class="form-select">
                                        <option value="">الكل</option>
                                        <option value="paid">مدفوعة</option>
                                        <option value="partial">مدفوعة جزئياً</option>
                                        <option value="unpaid">غير مدفوعة</option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-printer me-1"></i> عرض وطباعة التقرير
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- تقرير الأخصائيين -->
                    <div class="tab-pane fade" id="specialists" role="tabpanel">
                        <form method="GET" action="{{ route('admin.reports.specialists') }}" target="_blank">
                            <div class="row g-3 mb-3">
                                <div class="col-md-2">
                                    <label class="form-label">من تاريخ</label>
                                    <input type="date" name="date_from" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">إلى تاريخ</label>
                                    <input type="date" name="date_to" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">الأخصائي</label>
                                    <select name="specialist_id" class="form-select">
                                        <option value="">الكل</option>
                                        @foreach(\App\Models\User::role('specialist')->get() as $specialist)
                                            <option value="{{ $specialist->id }}">{{ $specialist->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-printer me-1"></i> عرض وطباعة التقرير
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- تقرير مؤشرات الخطر -->
                    <div class="tab-pane fade" id="risk" role="tabpanel">
                        <form method="GET" action="{{ route('admin.reports.risk') }}" target="_blank">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">نوع المؤشر</label>
                                    <select name="risk_type" class="form-select">
                                        <option value="">جميع المؤشرات</option>
                                        <option value="at_risk">طلاب في خطر (غياب متتالي)</option>
                                        <option value="without_case">بدون دراسة حالة</option>
                                        <option value="under_assessment">تحت التقييم</option>
                                        <option value="absent_3_days">غياب +3 أيام</option>
                                        <option value="absent_week">غياب +أسبوع</option>
                                    </select>
                                </div>
                                <div class="col-md-9 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-printer me-1"></i> عرض وطباعة التقرير
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
