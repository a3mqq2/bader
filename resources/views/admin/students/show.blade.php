@extends('layouts.app')

@section('title', 'بيانات الطالب')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            {{ $student->name }}
                            @if($student->is_at_risk)
                            <span class="badge bg-danger ms-2" title="غياب متتالي {{ $student->consecutive_unexcused_absence_days }} يوم بدون إذن">
                                <i class="fas fa-exclamation-triangle"></i> في خطر
                            </span>
                            @endif
                        </h5>
                        <span class="badge bg-secondary me-2">{{ $student->code }}</span>
                        <span class="badge bg-{{ $student->status_color }}">{{ $student->status_text }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        @if($student->status === 'new')
                        <button type="button" class="btn btn-sm btn-success" id="btnCaseStudy">
                            <i class="fas fa-clipboard-list me-1"></i> دراسة الحالة
                        </button>
                        @endif
                        @if($student->status !== 'new')
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info" onclick="openExcusedAbsenceModal()">
                                <i class="fas fa-calendar-times me-1"></i> غياب بإذن
                            </button>
                            @if($student->excusedAbsences->count() > 0)
                            <button type="button" class="btn btn-sm btn-info dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                                <span class="badge bg-light text-info">{{ $student->excusedAbsences->count() }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="openExcusedAbsenceList(); return false;"><i class="fas fa-list me-2"></i>عرض السجلات</a></li>
                            </ul>
                            @endif
                        </div>
                        @endif
                        <a href="{{ route('admin.students.card', $student) }}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fas fa-id-card me-1"></i> البطاقة
                        </a>
                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i> تعديل
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i> حذف
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="card mb-3">
            <div class="card-header p-0">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personal" role="tab">
                            <i class="fas fa-user me-1"></i> البيانات الشخصية
                        </a>
                    </li>
                    @if($student->currentCase)
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#case-study" role="tab">
                            <i class="fas fa-clipboard-check me-1"></i> دراسة الحالة
                        </a>
                    </li>
                    @endif
                    @if($student->status !== 'new')
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#invoices" role="tab">
                            <i class="fas fa-file-invoice-dollar me-1"></i> الفواتير
                            @if($student->invoices->count() > 0)
                            <span class="badge bg-primary ms-1">{{ $student->invoices->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#sessions" role="tab">
                            <i class="fas fa-calendar-check me-1"></i> الجلسات الفردية
                            @if($student->sessionPackages->count() > 0)
                            <span class="badge bg-info ms-1">{{ $student->sessionPackages->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#daycare" role="tab">
                            <i class="fas fa-sun me-1"></i> الرعاية النهارية
                            @if($student->daycareSubscriptions->count() > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $student->daycareSubscriptions->count() }}</span>
                            @endif
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            <div class="card-body">
                {{-- تنبيه غياب بإذن نشط --}}
                @if($student->activeExcusedAbsences->count() > 0)
                <div class="alert alert-info mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-check fa-2x me-3"></i>
                        <div>
                            <strong><i class="fas fa-info-circle me-1"></i> الطالب حالياً في فترة غياب بإذن</strong>
                            <div class="mt-1">
                                @foreach($student->activeExcusedAbsences as $absence)
                                <span class="badge bg-light text-dark me-1">
                                    {{ $absence->type_text }}: {{ $absence->start_date->format('Y/m/d') }} - {{ $absence->end_date->format('Y/m/d') }}
                                    ({{ $absence->reason_text }})
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- تنبيه مؤشر الخطر --}}
                @if($student->is_at_risk)
                <div class="alert alert-danger mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>تحذير:</strong> هذا الطالب لديه غياب متتالي <strong>{{ $student->consecutive_unexcused_absence_days }} يوم</strong> بدون إذن!
                            @if($student->last_attendance_date)
                            <small class="d-block mt-1 text-muted">آخر حضور: {{ $student->last_attendance_date->format('Y/m/d') }} (منذ {{ $student->days_since_last_attendance }} يوم)</small>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="openExcusedAbsenceModal()">
                            <i class="fas fa-calendar-times me-1"></i> تسجيل غياب بإذن
                        </button>
                    </div>
                </div>
                @endif

                @if($student->status === 'new' && !$student->currentCase)
                <div class="alert alert-warning text-center mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>تنبيه:</strong> يجب إجراء دراسة الحالة أولاً قبل إضافة أي فواتير أو جلسات للطالب.
                    <button type="button" class="btn btn-sm btn-success ms-3" id="btnCaseStudyAlert">
                        <i class="fas fa-clipboard-list me-1"></i> بدء دراسة الحالة
                    </button>
                </div>
                @endif
                <div class="tab-content">
                    <div class="tab-pane active" id="personal" role="tabpanel">
                        <div class="row">
                            <!-- معلومات الطالب -->
                            <div class="col-md-6 mb-3">
                                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user-graduate text-primary me-2"></i>معلومات الطالب</h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" width="35%"><i class="fas fa-signature me-1"></i> الاسم</td>
                                            <td class="fw-semibold">{{ $student->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-barcode me-1"></i> الكود</td>
                                            <td>{{ $student->code }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-calendar-alt me-1"></i> تاريخ الميلاد</td>
                                            <td>{{ $student->birth_date->format('Y/m/d') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-hourglass-half me-1"></i> العمر</td>
                                            <td>{{ $student->age }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-{{ $student->gender === 'male' ? 'mars' : 'venus' }} me-1"></i> الجنس</td>
                                            <td>{{ $student->gender_text }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-toggle-on me-1"></i> الحالة</td>
                                            <td><span class="badge bg-{{ $student->status_color }}">{{ $student->status_text }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- معلومات ولي الأمر -->
                            <div class="col-md-6 mb-3">
                                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user-friends text-primary me-2"></i>معلومات ولي الأمر</h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" width="35%"><i class="fas fa-user me-1"></i> الاسم</td>
                                            <td class="fw-semibold">{{ $student->guardian_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-phone me-1"></i> الهاتف</td>
                                            <td>
                                                {{ $student->phone }}
                                                <a href="tel:{{ $student->phone }}" class="btn btn-sm btn-outline-primary ms-2" title="اتصال">
                                                    <i class="fas fa-phone"></i>
                                                </a>
                                                <a href="https://wa.me/218{{ ltrim($student->phone, '0') }}" target="_blank" class="btn btn-sm btn-outline-success" title="واتساب">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @if($student->phone_alt)
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-phone-alt me-1"></i> هاتف بديل</td>
                                            <td>
                                                {{ $student->phone_alt }}
                                                <a href="tel:{{ $student->phone_alt }}" class="btn btn-sm btn-outline-primary ms-2" title="اتصال">
                                                    <i class="fas fa-phone"></i>
                                                </a>
                                                <a href="https://wa.me/218{{ ltrim($student->phone_alt, '0') }}" target="_blank" class="btn btn-sm btn-outline-success" title="واتساب">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($student->address)
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> العنوان</td>
                                            <td>{{ $student->address }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- ملاحظات -->
                            @if($student->notes)
                            <div class="col-md-6 mb-3">
                                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-sticky-note text-primary me-2"></i>ملاحظات</h6>
                                <p class="mb-0 small text-muted">{{ $student->notes }}</p>
                            </div>
                            @endif

                            <!-- معلومات النظام -->
                            <div class="col-md-6 mb-3">
                                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-cog text-primary me-2"></i>معلومات النظام</h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" width="35%"><i class="fas fa-calendar-plus me-1"></i> تاريخ التسجيل</td>
                                            <td>{{ $student->created_at->format('Y/m/d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-clock me-1"></i> آخر تحديث</td>
                                            <td>{{ $student->updated_at->format('Y/m/d H:i') }}</td>
                                        </tr>
                                        @if($student->creator)
                                        <tr>
                                            <td class="text-muted"><i class="fas fa-user-plus me-1"></i> أضيف بواسطة</td>
                                            <td>{{ $student->creator->name }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- تاب دراسة الحالة -->
                    @if($student->currentCase)
                    @php
                        $case = $student->currentCase;
                        // نحسب فقط المقاييس (بدون دراسة الحالة id=1)
                        $assessmentItems = $case->invoice ? $case->invoice->items->where('assessment_id', '!=', 1) : collect();
                        $completedCount = $assessmentItems->where('assessment_status', 'completed')->count();
                        $totalCount = $assessmentItems->count();
                        $progressPercent = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 100;
                    @endphp
                    <div class="tab-pane" id="case-study" role="tabpanel">
                        <div class="row">
                            <!-- بطاقة المقاييس والتقييمات - على اليمين -->
                            <div class="col-lg-8 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-list-check text-primary me-2"></i>
                                            المقاييس والتقييمات
                                        </h6>
                                        @if($totalCount > 0)
                                        <span class="badge bg-{{ $progressPercent == 100 ? 'success' : 'warning' }}">
                                            {{ $progressPercent == 100 ? 'مكتمل' : 'جاري التقييم' }} ({{ $completedCount }}/{{ $totalCount }})
                                        </span>
                                        @endif
                                    </div>
                                    <div class="card-body p-0">
                                        @if($case->invoice && $case->invoice->items->count() > 0)
                                        <div class="list-group list-group-flush">
                                            @php $assessmentIndex = 0; @endphp
                                            @foreach($case->invoice->items as $item)
                                            <div class="list-group-item" id="assessment-item-{{ $item->id }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center flex-grow-1">
                                                        @if($item->assessment_id == 1)
                                                        {{-- دراسة الحالة --}}
                                                        <span class="badge bg-secondary rounded-pill me-3">
                                                            <i class="fas fa-clipboard-check"></i>
                                                        </span>
                                                        <div>
                                                            <h6 class="mb-0">{{ $item->assessment_name }}</h6>
                                                            <small class="text-muted">دراسة الحالة الأساسية</small>
                                                        </div>
                                                        @else
                                                        {{-- المقاييس الأخرى --}}
                                                        @php $assessmentIndex++; @endphp
                                                        <span class="badge bg-{{ $item->assessment_status == 'completed' ? 'success' : 'primary' }} rounded-pill me-3">
                                                            @if($item->assessment_status == 'completed')
                                                            <i class="fas fa-check"></i>
                                                            @else
                                                            {{ $assessmentIndex }}
                                                            @endif
                                                        </span>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $item->assessment_name }}</h6>
                                                            <span class="badge bg-{{ $item->assessment_status_color }} mt-1">{{ $item->assessment_status_text }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    {{-- زر التقييم فقط للمقاييس (ليس دراسة الحالة) --}}
                                                    @if($item->assessment_id != 1)
                                                    <div class="ms-2">
                                                        <button type="button"
                                                                class="btn btn-sm btn-{{ $item->assessment_status == 'completed' ? 'outline-primary' : 'primary' }}"
                                                                onclick="openAssessmentModal({{ $item->id }})"
                                                                title="{{ $item->assessment_status == 'completed' ? 'تعديل التقييم' : 'إضافة تقييم' }}">
                                                            <i class="fas fa-{{ $item->assessment_status == 'completed' ? 'edit' : 'plus' }} me-1"></i>
                                                            {{ $item->assessment_status == 'completed' ? 'تعديل' : 'تقييم' }}
                                                        </button>
                                                    </div>
                                                    @endif
                                                </div>

                                                {{-- نتيجة التقييم --}}
                                                @if($item->assessment_id != 1 && $item->assessment_status == 'completed')
                                                <div class="mt-3 p-3 bg-light rounded border-start border-3 border-success">
                                                    <div class="mb-2">
                                                        <strong class="text-dark"><i class="fas fa-file-alt me-1 text-success"></i>النتيجة:</strong>
                                                        <p class="mb-0 mt-1">{{ $item->assessment_result }}</p>
                                                    </div>
                                                    @if($item->assessment_notes)
                                                    <div class="mb-2">
                                                        <strong class="text-muted"><i class="fas fa-comment me-1"></i>ملاحظات:</strong>
                                                        <p class="mb-0 mt-1 text-muted">{{ $item->assessment_notes }}</p>
                                                    </div>
                                                    @endif
                                                    <hr class="my-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>{{ $item->assessor->name ?? '-' }}
                                                        <span class="mx-2">|</span>
                                                        <i class="fas fa-calendar me-1"></i>{{ $item->assessed_at ? $item->assessed_at->format('Y/m/d h:i A') : '-' }}
                                                    </small>
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                        @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">لا توجد مقاييس</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- بطاقة معلومات دراسة الحالة - على اليسار -->
                            <div class="col-lg-4 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-0">دراسة الحالة</h6>
                                                <span class="badge bg-{{ $case->status_color }}">{{ $case->status_text }}</span>
                                            </div>
                                        </div>

                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex justify-content-between py-2 border-bottom">
                                                <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i>التاريخ</span>
                                                <strong>{{ $case->created_at->format('Y/m/d') }}</strong>
                                            </li>
                                            <li class="d-flex justify-content-between py-2 border-bottom">
                                                <span class="text-muted"><i class="fas fa-clock me-2"></i>الوقت</span>
                                                <strong>{{ $case->created_at->format('h:i A') }}</strong>
                                            </li>
                                            <li class="d-flex justify-content-between py-2">
                                                <span class="text-muted"><i class="fas fa-user me-2"></i>بواسطة</span>
                                                <strong>{{ $case->creator->name ?? '-' }}</strong>
                                            </li>
                                        </ul>

                                        @if($totalCount > 0)
                                        <hr>
                                        <div class="mb-2 d-flex justify-content-between">
                                            <span class="text-muted">تقدم التقييمات</span>
                                            <span class="fw-bold">{{ $completedCount }}/{{ $totalCount }}</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        @endif

                                        @if($case->notes)
                                        <hr>
                                        <div>
                                            <small class="text-muted"><i class="fas fa-sticky-note me-1"></i>ملاحظات:</small>
                                            <p class="mb-0 mt-1 small">{{ $case->notes }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($student->status !== 'new')
                    <!-- تاب الفواتير -->
                    <div class="tab-pane" id="invoices" role="tabpanel">
                        <!-- زر إضافة فاتورة -->
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                                <i class="fas fa-plus me-1"></i> إنشاء فاتورة
                            </button>
                        </div>

                        @if($student->invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>الوصف</th>
                                        <th>النوع</th>
                                        <th>التاريخ</th>
                                        <th>الإجمالي</th>
                                        <th>الحالة</th>
                                        <th width="10%" class="text-center">عرض</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->invoices as $index => $invoice)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                                            @if($invoice->description)
                                            <small class="text-muted">{{ $invoice->description }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $invoice->type_text }}</span></td>
                                        <td>{{ $invoice->created_at->format('Y/m/d') }}</td>
                                        <td class="fw-bold">{{ $invoice->formatted_total }}</td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->status_color }}">{{ $invoice->status_text }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#invoiceModal{{ $invoice->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice fa-4x mb-3 opacity-25"></i>
                            <p class="mb-0">لا توجد فواتير بعد</p>
                            <small>اضغط على زر "إنشاء فاتورة" لإضافة فاتورة جديدة</small>
                        </div>
                        @endif
                    </div>

                    <!-- تاب الجلسات الفردية -->
                    <div class="tab-pane" id="sessions" role="tabpanel">
                        <!-- زر إضافة باقة جلسات -->
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn text-white" style="background: #063973;" data-bs-toggle="modal" data-bs-target="#createSessionPackageModal">
                                <i class="fas fa-plus me-1"></i> إضافة باقة جلسات
                            </button>
                        </div>

                        @if($student->sessionPackages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead style="background: #063973; color: white;">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>نوع الجلسة</th>
                                        <th>الأخصائي</th>
                                        <th>الفترة</th>
                                        <th>الوقت</th>
                                        <th class="text-center">الجلسات</th>
                                        <th>الإجمالي</th>
                                        <th width="12%" class="text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->sessionPackages->sortByDesc('created_at') as $index => $package)
                                    @php
                                        $totalSessions = $package->sessions->count();
                                        $completedCount = $package->sessions->where('status', 'completed')->count();
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $package->therapySession->name ?? '-' }}</strong>
                                            <br><small class="text-muted">{{ $package->days_text }}</small>
                                        </td>
                                        <td>{{ $package->specialist->name ?? '-' }}</td>
                                        <td>
                                            <small>{{ $package->start_date->format('Y/m/d') }}</small>
                                            <br><small>{{ $package->end_date->format('Y/m/d') }}</small>
                                        </td>
                                        <td>{{ date('h:i A', strtotime($package->session_time)) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $completedCount }}</span>
                                            /
                                            <span class="badge bg-secondary">{{ $totalSessions }}</span>
                                        </td>
                                        <td class="fw-bold">{{ number_format($package->total_price, 2) }} د.ل</td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.session-packages.show', $package) }}" class="btn btn-outline-primary" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.session-packages.print', $package) }}" target="_blank" class="btn btn-outline-secondary" title="طباعة">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" onclick="deletePackage({{ $package->id }})" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-check fa-4x mb-3 opacity-25"></i>
                            <p class="mb-0">لا توجد باقات جلسات بعد</p>
                            <small>اضغط على زر "إضافة باقة جلسات" لإنشاء جدول جلسات جديد</small>
                        </div>
                        @endif
                    </div>

                    <!-- تاب الرعاية النهارية -->
                    <div class="tab-pane" id="daycare" role="tabpanel">
                        <!-- زر إضافة اشتراك -->
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn text-white" style="background: #063973;" data-bs-toggle="modal" data-bs-target="#createDaycareModal">
                                <i class="fas fa-plus me-1"></i> إضافة اشتراك رعاية نهارية
                            </button>
                        </div>

                        @if($student->daycareSubscriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead style="background: #063973; color: white;">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>نوع الرعاية</th>
                                        <th>المشرف</th>
                                        <th>الفترة</th>
                                        <th class="text-center">الحضور</th>
                                        <th>السعر</th>
                                        <th>الحالة</th>
                                        <th width="12%" class="text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->daycareSubscriptions->sortByDesc('created_at') as $index => $subscription)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $subscription->daycareType->name ?? '-' }}</strong>
                                        </td>
                                        <td>{{ $subscription->supervisor->name ?? '-' }}</td>
                                        <td>
                                            <small>{{ $subscription->start_date->format('Y/m/d') }}</small>
                                            <br><small>{{ $subscription->end_date->format('Y/m/d') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $subscription->present_count }}</span>
                                            /
                                            <span class="badge bg-secondary">{{ $subscription->attendances->count() }}</span>
                                        </td>
                                        <td class="fw-bold">{{ $subscription->formatted_price }}</td>
                                        <td>
                                            <span class="badge bg-{{ $subscription->status_color }}">{{ $subscription->status_text }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.daycare.show', $subscription) }}" class="btn btn-outline-primary" title="عرض الحضور">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($subscription->status === 'active')
                                                <button type="button" class="btn btn-outline-warning" onclick="cancelDaycare({{ $subscription->id }})" title="إلغاء">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger" onclick="deleteDaycare({{ $subscription->id }})" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-sun fa-4x mb-3 opacity-25"></i>
                            <p class="mb-0">لا توجد اشتراكات رعاية نهارية بعد</p>
                            <small>اضغط على زر "إضافة اشتراك رعاية نهارية" لإنشاء اشتراك جديد</small>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Modals -->
@foreach($student->invoices as $invoice)
<div class="modal fade" id="invoiceModal{{ $invoice->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: #063973;">
                        <i class="fas fa-file-invoice text-white fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold">{{ $invoice->invoice_number }}</h5>
                        <small class="text-muted">{{ $invoice->created_at->format('Y/m/d - h:i A') }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-4">
                <!-- معلومات الفاتورة -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: #f8f9fa;">
                            <h6 class="mb-3" style="color: #063973;"><i class="fas fa-user me-2"></i>بيانات الطالب</h6>
                            <p class="mb-1"><strong>{{ $student->name }}</strong></p>
                            <p class="mb-1 text-muted small">{{ $student->code }}</p>
                            <p class="mb-0 text-muted small">{{ $student->phone }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: #f8f9fa;">
                            <h6 class="mb-3" style="color: #063973;"><i class="fas fa-info-circle me-2"></i>حالة الفاتورة</h6>
                            <div class="mb-2">
                                <span class="badge bg-{{ $invoice->status_color }} fs-6">{{ $invoice->status_text }}</span>
                                <span class="badge bg-secondary ms-1">{{ $invoice->type_text }}</span>
                            </div>
                            @if($invoice->description)
                            <p class="mb-0 text-muted small">{{ $invoice->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- بنود الفاتورة -->
                <h6 class="mb-3" style="color: #063973;"><i class="fas fa-list me-2"></i>بنود الفاتورة</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered mb-0">
                        <thead style="background: #063973; color: white;">
                            <tr>
                                <th width="5%">#</th>
                                <th>البند</th>
                                <th width="15%" class="text-center">السعر</th>
                                <th width="10%" class="text-center">الكمية</th>
                                <th width="15%" class="text-center">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->assessment_name }}</td>
                                <td class="text-center">{{ number_format($item->price, 2) }} د.ل</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-center fw-bold">{{ number_format($item->total, 2) }} د.ل</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- ملخص مالي -->
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="p-3 rounded border">
                            <div class="d-flex justify-content-between mb-2">
                                <span>الإجمالي</span>
                                <strong style="color: #063973;">{{ $invoice->formatted_total }}</strong>
                            </div>
                            @if($invoice->discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>الخصم</span>
                                <span>- {{ number_format($invoice->discount, 2) }} د.ل</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>المدفوع</span>
                                <span>{{ $invoice->formatted_paid }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <strong>المتبقي</strong>
                                <strong class="text-danger fs-5">{{ $invoice->formatted_balance }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                @if($invoice->notes)
                <div class="mt-3 p-3 rounded border-start border-3" style="background: #fff3cd; border-color: #ffc107 !important;">
                    <strong><i class="fas fa-sticky-note me-1"></i> ملاحظات:</strong>
                    <p class="mb-0 mt-1">{{ $invoice->notes }}</p>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إغلاق
                </button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" onclick="openEditInvoiceModal({{ $invoice->id }})">
                    <i class="fas fa-edit me-1"></i> تعديل
                </button>
                <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" class="btn text-white" style="background: #063973;">
                    <i class="fas fa-print me-1"></i> طباعة
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Edit Invoice Modal -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: #063973;">
                <h5 class="modal-title text-white"><i class="fas fa-edit me-2"></i>تعديل الفاتورة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="editInvoiceLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
                <form id="editInvoiceForm" style="display: none;">
                    @csrf
                    <input type="hidden" id="editInvoiceId" name="invoice_id">

                    <div class="row">
                        <div class="col-md-4">
                            <!-- نوع الفاتورة -->
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-tag me-1"></i> نوع الفاتورة</label>
                                <select name="invoice_type_id" id="editInvoiceTypeId" class="form-select">
                                    <option value="">-- اختر النوع --</option>
                                    @foreach(\App\Models\InvoiceType::active()->get() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- الوصف -->
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-align-left me-1"></i> الوصف</label>
                                <input type="text" name="description" id="editInvoiceDescription" class="form-control" placeholder="وصف الفاتورة">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- ملاحظات -->
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-sticky-note me-1"></i> ملاحظات</label>
                                <input type="text" name="notes" id="editInvoiceNotes" class="form-control" placeholder="أي ملاحظات إضافية...">
                            </div>
                        </div>
                    </div>

                    <!-- عناصر الفاتورة -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0"><i class="fas fa-list me-1"></i> عناصر الفاتورة</label>
                            <button type="button" class="btn btn-sm btn-success" id="btnAddInvoiceItem">
                                <i class="fas fa-plus me-1"></i> إضافة عنصر
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>البند</th>
                                        <th width="15%">السعر</th>
                                        <th width="10%">الكمية</th>
                                        <th width="15%">الإجمالي</th>
                                        <th width="8%">حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="editInvoiceItemsBody">
                                    <!-- سيتم ملؤها ديناميكياً -->
                                </tbody>
                                <tfoot style="background: #f8f9fa;">
                                    <tr>
                                        <td colspan="4" class="text-start fw-bold">إجمالي العناصر</td>
                                        <td class="fw-bold" id="editInvoiceItemsTotal" style="color: #063973;">0.00 د.ل</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- المبلغ المدفوع -->
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-money-bill-wave me-1"></i> المبلغ المدفوع</label>
                                <div class="input-group">
                                    <input type="number" name="paid_amount" id="editInvoicePaidAmount" class="form-control" placeholder="0.00" step="0.01" min="0">
                                    <span class="input-group-text">د.ل</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- الخصم -->
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-percent me-1"></i> الخصم</label>
                                <div class="input-group">
                                    <input type="number" name="discount" id="editInvoiceDiscount" class="form-control" placeholder="0.00" step="0.01" min="0">
                                    <span class="input-group-text">د.ل</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ملخص مالي -->
                    <div class="card border-0" style="background: linear-gradient(135deg, #063973 0%, #0a5299 100%);">
                        <div class="card-body py-3">
                            <div class="row text-center text-white">
                                <div class="col-4">
                                    <small class="opacity-75 d-block">الإجمالي</small>
                                    <strong id="editInvoiceTotalDisplay" class="fs-5">0.00 د.ل</strong>
                                </div>
                                <div class="col-4">
                                    <small class="opacity-75 d-block">المدفوع + الخصم</small>
                                    <strong id="editInvoicePaidDisplay" class="fs-5">0.00 د.ل</strong>
                                </div>
                                <div class="col-4">
                                    <small class="opacity-75 d-block">المتبقي</small>
                                    <strong id="editInvoiceBalanceDisplay" class="fs-5">0.00 د.ل</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إلغاء
                </button>
                <button type="button" class="btn btn-danger me-auto" id="btnDeleteInvoice">
                    <i class="fas fa-trash me-1"></i> حذف
                </button>
                <button type="button" class="btn text-white" style="background: #063973;" id="btnUpdateInvoice">
                    <i class="fas fa-save me-1"></i> حفظ التغييرات
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Assessment Modal -->
@if($student->currentCase)
<div class="modal fade" id="assessmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-clipboard-check me-2"></i>
                    <span id="assessmentModalTitle">التقييم</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="assessmentLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
                <div id="assessmentFormContainer" style="display: none;">
                    <form id="assessmentForm">
                        @csrf
                        <input type="hidden" id="assessmentItemId" name="item_id">

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-file-alt me-1 text-primary"></i> النتيجة <span class="text-danger">*</span>
                            </label>
                            <textarea name="assessment_result" id="assessmentResult" class="form-control" rows="4"
                                      placeholder="اكتب نتيجة التقييم هنا..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-comment me-1 text-muted"></i> ملاحظات إضافية
                            </label>
                            <textarea name="assessment_notes" id="assessmentNotes" class="form-control" rows="2"
                                      placeholder="أي ملاحظات إضافية (اختياري)..."></textarea>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إلغاء
                </button>
                <button type="button" class="btn btn-success" id="btnSaveAssessment" onclick="saveAssessment()">
                    <i class="fas fa-save me-1"></i> حفظ التقييم
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Create Invoice Modal -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #063973;">
                <h5 class="modal-title text-white"><i class="fas fa-file-invoice me-2"></i>إنشاء فاتورة جديدة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createInvoiceForm">
                    @csrf
                    <!-- نوع الفاتورة -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-tag me-1"></i> نوع الفاتورة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="invoice_type_id" id="invoiceTypeId" class="form-select" required>
                                <option value="">-- اختر النوع --</option>
                                @foreach(\App\Models\InvoiceType::active()->get() as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addInvoiceTypeModal" title="إضافة نوع جديد">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- الوصف -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-align-left me-1"></i> الوصف <span class="text-danger">*</span></label>
                        <input type="text" name="description" id="invoiceDescription" class="form-control" placeholder="مثال: رسوم شهر يناير" required>
                    </div>

                    <!-- القيمة -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-money-bill me-1"></i> القيمة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" id="invoiceAmount" class="form-control" placeholder="0.00" step="0.01" min="0.01" required>
                            <span class="input-group-text">د.ل</span>
                        </div>
                    </div>

                    <!-- ملاحظات -->
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-sticky-note me-1"></i> ملاحظات (اختياري)</label>
                        <textarea name="notes" id="invoiceNotes" class="form-control" rows="2" placeholder="أي ملاحظات إضافية..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إلغاء
                </button>
                <button type="button" class="btn text-white" style="background: #063973;" id="btnSaveInvoice">
                    <i class="fas fa-save me-1"></i> حفظ الفاتورة
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Invoice Type Modal -->
<div class="modal fade" id="addInvoiceTypeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header text-white py-2" style="background: #063973;">
                <h6 class="modal-title text-white"><i class="fas fa-plus me-1"></i> إضافة نوع فاتورة</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">اسم النوع <span class="text-danger">*</span></label>
                    <input type="text" id="newInvoiceTypeName" class="form-control" placeholder="مثال: رسوم إضافية">
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-sm text-white" style="background: #063973;" id="btnSaveInvoiceType">
                    <i class="fas fa-save me-1"></i> حفظ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title"><i class="fas fa-exclamation-triangle me-1"></i> تأكيد الحذف</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <p class="mb-1">هل أنت متأكد من حذف:</p>
                <strong>{{ $student->name }}</strong>
            </div>
            <div class="modal-footer justify-content-center py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> إلغاء</button>
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash me-1"></i> حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($student->status === 'new')
<!-- Case Study Modal -->
<div class="modal fade" id="caseStudyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #063973 0%, #0a5299 100%);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2);">
                        <i class="fas fa-clipboard-list text-white fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 text-white fw-bold">إنشاء دراسة حالة</h5>
                        <small class="opacity-75">{{ $student->name }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="caseStudyLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-3 text-muted mb-0">جاري تحميل البيانات...</p>
                </div>
                <div id="caseStudyContent" style="display: none;">
                    <form id="caseStudyForm">
                        @csrf

                        <!-- بطاقة معلومات الطالب -->
                        <div class="card border-0 mb-4" style="background: #f8f9fa;">
                            <div class="card-body py-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #063973;">
                                            <i class="fas fa-user-graduate text-white"></i>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h6 class="mb-0 fw-bold">{{ $student->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-barcode me-1"></i>{{ $student->code }}
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-calendar me-1"></i>{{ $student->age }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- دراسة الحالة الأساسية -->
                        <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: #063973;">
                                            <i class="fas fa-clipboard-check text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold" id="caseStudyName">دراسة الحالة</h6>
                                            <span class="badge bg-primary">إجباري</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="fs-5 fw-bold" style="color: #063973;" id="caseStudyPrice">0.00</span>
                                        <small class="text-muted d-block">د.ل</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- المقاييس الإضافية -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-list-check me-2" style="color: #063973;"></i>
                                    المقاييس والتقييمات الإضافية
                                </h6>
                                <small class="text-muted">(اختياري)</small>
                            </div>
                            <div id="assessmentsList" class="border rounded-3" style="max-height: 280px; overflow-y: auto;">
                                <div class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                    <p class="text-muted mb-0 mt-2 small">جاري التحميل...</p>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                اختر المقاييس المطلوبة لإضافتها للفاتورة
                            </small>
                        </div>

                        <!-- الملاحظات -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-sticky-note me-1" style="color: #063973;"></i>
                                ملاحظات
                            </label>
                            <textarea name="notes" class="form-control border-2" rows="2" placeholder="أي ملاحظات إضافية على دراسة الحالة..." style="border-color: #e0e0e0;"></textarea>
                        </div>

                        <!-- المجموع -->
                        <div class="card border-0" style="background: linear-gradient(135deg, #063973 0%, #0a5299 100%);">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center text-white">
                                    <div>
                                        <i class="fas fa-calculator me-2"></i>
                                        <span class="fw-bold">إجمالي الفاتورة</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="fs-4 fw-bold" id="totalAmount">0.00</span>
                                        <small class="opacity-75 d-block">دينار ليبي</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="caseStudyError" style="display: none;" class="alert alert-danger mb-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                        <div>
                            <strong>حدث خطأ!</strong>
                            <p class="mb-0" id="errorMessage"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إلغاء
                </button>
                <button type="button" class="btn text-white px-4" style="background: #063973;" id="btnSaveCaseStudy" disabled>
                    <i class="fas fa-save me-1"></i> حفظ وإنشاء الفاتورة
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Create Session Package Modal -->
<div class="modal fade" id="createSessionPackageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #063973;">
                <h5 class="modal-title text-white"><i class="fas fa-calendar-plus me-2"></i>إضافة باقة جلسات</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sessionPackageForm">
                    @csrf
                    <div class="row">
                        <!-- العمود الأيسر - بيانات الباقة -->
                        <div class="col-md-5">
                            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-1" style="color: #063973;"></i> بيانات الباقة</h6>

                            <!-- نوع الجلسة -->
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-tag me-1"></i> نوع الجلسة <span class="text-danger">*</span></label>
                                <select name="therapy_session_id" id="therapySessionId" class="form-select" required>
                                    <option value="">-- اختر نوع الجلسة --</option>
                                    @foreach(\App\Models\TherapySession::active()->get() as $therapySession)
                                    <option value="{{ $therapySession->id }}" data-price="{{ $therapySession->price }}">
                                        {{ $therapySession->name }} - {{ $therapySession->formatted_price }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- الأخصائي -->
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-user-md me-1"></i> الأخصائي <span class="text-danger">*</span></label>
                                <select name="specialist_id" id="specialistId" class="form-select" required>
                                    <option value="">-- اختر الأخصائي --</option>
                                    @foreach(\App\Models\User::role('specialist')->get() as $specialist)
                                    <option value="{{ $specialist->id }}">{{ $specialist->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- من تاريخ - إلى تاريخ -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold"><i class="fas fa-calendar-alt me-1"></i> من تاريخ <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="startDate" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold"><i class="fas fa-calendar-alt me-1"></i> إلى تاريخ <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="endDate" class="form-control" required>
                                </div>
                            </div>

                            <!-- الوقت ومدة الجلسة -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold"><i class="fas fa-clock me-1"></i> وقت الجلسة <span class="text-danger">*</span></label>
                                    <input type="time" name="session_time" id="sessionTime" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold"><i class="fas fa-hourglass-half me-1"></i> المدة (دقيقة)</label>
                                    <input type="number" name="session_duration" id="sessionDuration" class="form-control" value="30" min="15" max="120">
                                </div>
                            </div>

                            <!-- أيام الأسبوع -->
                            <div class="mb-3">
                                <label class="form-label fw-bold"><i class="fas fa-calendar-week me-1"></i> أيام الجلسات <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input day-checkbox" type="checkbox" name="days[]" value="saturday" id="daySaturday">
                                        <label class="form-check-label" for="daySaturday">السبت</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input day-checkbox" type="checkbox" name="days[]" value="sunday" id="daySunday">
                                        <label class="form-check-label" for="daySunday">الأحد</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input day-checkbox" type="checkbox" name="days[]" value="monday" id="dayMonday">
                                        <label class="form-check-label" for="dayMonday">الإثنين</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input day-checkbox" type="checkbox" name="days[]" value="tuesday" id="dayTuesday">
                                        <label class="form-check-label" for="dayTuesday">الثلاثاء</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input day-checkbox" type="checkbox" name="days[]" value="wednesday" id="dayWednesday">
                                        <label class="form-check-label" for="dayWednesday">الأربعاء</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input day-checkbox" type="checkbox" name="days[]" value="thursday" id="dayThursday">
                                        <label class="form-check-label" for="dayThursday">الخميس</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input day-checkbox" type="checkbox" name="days[]" value="friday" id="dayFriday">
                                        <label class="form-check-label" for="dayFriday">الجمعة</label>
                                    </div>
                                </div>
                            </div>

                            <!-- ملاحظات -->
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-sticky-note me-1"></i> ملاحظات (اختياري)</label>
                                <textarea name="notes" id="packageNotes" class="form-control" rows="2" placeholder="أي ملاحظات إضافية..."></textarea>
                            </div>

                            <!-- زر معاينة -->
                            <button type="button" class="btn text-white w-100" style="background: #063973;" id="btnPreviewSessions">
                                <i class="fas fa-eye me-1"></i> معاينة الجلسات
                            </button>
                        </div>

                        <!-- العمود الأيمن - معاينة الجلسات -->
                        <div class="col-md-7">
                            <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-list me-1" style="color: #063973;"></i> معاينة الجلسات</h6>

                            <div id="sessionsPreviewContainer">
                                <div class="text-center py-5 text-muted" id="noPreviewMessage">
                                    <i class="fas fa-calendar-alt fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">أدخل البيانات واضغط على "معاينة الجلسات"</p>
                                    <small>سيتم عرض جدول الجلسات هنا</small>
                                </div>

                                <div id="sessionsPreviewLoading" class="text-center py-5" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">جاري التحميل...</span>
                                    </div>
                                    <p class="mt-2 text-muted">جاري توليد الجلسات...</p>
                                </div>

                                <div id="sessionsPreviewTable" style="display: none;">
                                    <div class="alert alert-info py-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-info-circle me-1"></i> عدد الجلسات: <strong id="sessionsCount">0</strong></span>
                                            <span>الإجمالي: <strong id="totalPrice" style="color: #063973;">0.00 د.ل</strong></span>
                                        </div>
                                    </div>
                                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th>اليوم</th>
                                                    <th>التاريخ</th>
                                                    <th>الوقت</th>
                                                    <th width="10%">حذف</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sessionsPreviewBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> إلغاء
                </button>
                <button type="button" class="btn btn-success" id="btnSaveSessionPackage" disabled>
                    <i class="fas fa-save me-1"></i> حفظ الباقة
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Package Modal -->
<div class="modal fade" id="deletePackageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title"><i class="fas fa-exclamation-triangle me-1"></i> تأكيد حذف الباقة</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <p class="mb-1">هل أنت متأكد من حذف هذه الباقة؟</p>
                <small class="text-danger">سيتم حذف جميع الجلسات المرتبطة بها</small>
            </div>
            <div class="modal-footer justify-content-center py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnConfirmDeletePackage">
                    <i class="fas fa-trash me-1"></i> حذف
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Daycare Subscription Modal -->
<div class="modal fade" id="createDaycareModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: #063973;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-sun me-2"></i> إضافة اشتراك رعاية نهارية
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="daycareForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نوع الرعاية <span class="text-danger">*</span></label>
                        <select name="daycare_type_id" id="daycareTypeSelect" class="form-select" required>
                            <option value="">-- اختر نوع الرعاية --</option>
                            @foreach(\App\Models\DaycareType::active()->get() as $type)
                            <option value="{{ $type->id }}" data-price="{{ $type->price }}">{{ $type->name }} ({{ $type->formatted_price }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المشرف <span class="text-danger">*</span></label>
                        <select name="supervisor_id" class="form-select" required>
                            <option value="">-- اختر المشرف --</option>
                            @foreach(\App\Models\User::where('is_active', true)->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ النهاية <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات إضافية..."></textarea>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>سيتم توليد أيام الحضور تلقائياً (عدا الجمعة والسبت)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn text-white" style="background: #063973;">
                        <i class="fas fa-save me-1"></i> حفظ الاشتراك
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Daycare Delete Modal -->
<div class="modal fade" id="deleteDaycareModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title"><i class="fas fa-exclamation-triangle me-1"></i> تأكيد الحذف</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <p class="mb-1">هل أنت متأكد من حذف هذا الاشتراك؟</p>
                <small class="text-danger">سيتم حذف جميع سجلات الحضور المرتبطة</small>
            </div>
            <div class="modal-footer justify-content-center py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnConfirmDeleteDaycare">
                    <i class="fas fa-trash me-1"></i> حذف
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Daycare Cancel Modal -->
<div class="modal fade" id="cancelDaycareModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-warning py-2">
                <h6 class="modal-title"><i class="fas fa-ban me-1"></i> تأكيد الإلغاء</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <p class="mb-0">هل أنت متأكد من إلغاء هذا الاشتراك؟</p>
            </div>
            <div class="modal-footer justify-content-center py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">رجوع</button>
                <button type="button" class="btn btn-sm btn-warning" id="btnConfirmCancelDaycare">
                    <i class="fas fa-ban me-1"></i> إلغاء الاشتراك
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal تسجيل غياب بإذن -->
<div class="modal fade" id="excusedAbsenceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="fas fa-calendar-times me-2"></i>تسجيل غياب بإذن</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="excusedAbsenceForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نوع الغياب <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="">-- اختر النوع --</option>
                            <option value="sessions">جلسات</option>
                            <option value="daycare">رعاية نهارية</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">من تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">إلى تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سبب الغياب <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select" required onchange="toggleReasonDetails(this)">
                            <option value="">-- اختر السبب --</option>
                            <option value="illness">مرض</option>
                            <option value="travel">سفر</option>
                            <option value="family">ظرف عائلي</option>
                            <option value="other">سبب آخر</option>
                        </select>
                    </div>
                    <div class="mb-3" id="reasonDetailsWrapper" style="display: none;">
                        <label class="form-label">تفاصيل السبب <span class="text-danger">*</span></label>
                        <textarea name="reason_details" class="form-control" rows="2" placeholder="يرجى كتابة تفاصيل السبب..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="أي ملاحظات إضافية..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-info" id="btnSaveExcusedAbsence">
                        <i class="fas fa-save me-1"></i> حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal قائمة الغياب بإذن -->
<div class="modal fade" id="excusedAbsenceListModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="fas fa-list me-2"></i>سجل الغياب بإذن</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="excusedAbsenceListContent">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-info" onclick="openExcusedAbsenceModal()">
                    <i class="fas fa-plus me-1"></i> إضافة غياب بإذن
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// حفظ واستعادة التاب النشط
document.addEventListener('DOMContentLoaded', function() {
    const tabKey = 'student_{{ $student->id }}_active_tab';

    // أولوية لـ URL hash إذا وجد
    let activeTab = window.location.hash || localStorage.getItem(tabKey);
    if (activeTab) {
        const tabElement = document.querySelector(`a[href="${activeTab}"]`);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }

    // حفظ التاب النشط عند التغيير
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function(tabLink) {
        tabLink.addEventListener('shown.bs.tab', function(e) {
            localStorage.setItem(tabKey, e.target.getAttribute('href'));
        });
    });

    // زر دراسة الحالة من التنبيه
    const btnCaseStudyAlert = document.getElementById('btnCaseStudyAlert');
    if (btnCaseStudyAlert) {
        btnCaseStudyAlert.addEventListener('click', function() {
            const btnCaseStudy = document.getElementById('btnCaseStudy');
            if (btnCaseStudy) {
                btnCaseStudy.click();
            }
        });
    }
});

// === إنشاء الفاتورة اليدوية ===
document.addEventListener('DOMContentLoaded', function() {
    const btnSaveInvoice = document.getElementById('btnSaveInvoice');

    // حفظ الفاتورة
    btnSaveInvoice.addEventListener('click', function() {
        const invoiceTypeId = document.getElementById('invoiceTypeId').value;
        const description = document.getElementById('invoiceDescription').value.trim();
        const amount = parseFloat(document.getElementById('invoiceAmount').value) || 0;
        const notes = document.getElementById('invoiceNotes').value;

        // التحقق من البيانات
        if (!invoiceTypeId) {
            alert('يرجى اختيار نوع الفاتورة');
            document.getElementById('invoiceTypeId').focus();
            return;
        }
        if (!description) {
            alert('يرجى إدخال الوصف');
            document.getElementById('invoiceDescription').focus();
            return;
        }
        if (amount <= 0) {
            alert('يرجى إدخال القيمة');
            document.getElementById('invoiceAmount').focus();
            return;
        }

        btnSaveInvoice.disabled = true;
        btnSaveInvoice.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

        fetch('{{ route("admin.students.invoices.store", $student) }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                invoice_type_id: invoiceTypeId,
                description: description,
                amount: amount,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                localStorage.setItem('student_{{ $student->id }}_active_tab', '#invoices');
                window.location.reload();
            } else {
                btnSaveInvoice.disabled = false;
                btnSaveInvoice.innerHTML = '<i class="fas fa-save me-1"></i> حفظ الفاتورة';
                alert(data.message || 'حدث خطأ أثناء الحفظ');
            }
        })
        .catch(error => {
            btnSaveInvoice.disabled = false;
            btnSaveInvoice.innerHTML = '<i class="fas fa-save me-1"></i> حفظ الفاتورة';
            alert('حدث خطأ في الاتصال بالخادم');
            console.error(error);
        });
    });

    // إعادة تعيين النموذج عند إغلاق Modal
    document.getElementById('createInvoiceModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('createInvoiceForm').reset();
        btnSaveInvoice.disabled = false;
        btnSaveInvoice.innerHTML = '<i class="fas fa-save me-1"></i> حفظ الفاتورة';
    });

    // === إضافة نوع فاتورة جديد ===
    const btnSaveInvoiceType = document.getElementById('btnSaveInvoiceType');
    const addInvoiceTypeModal = new bootstrap.Modal(document.getElementById('addInvoiceTypeModal'));

    btnSaveInvoiceType.addEventListener('click', function() {
        const name = document.getElementById('newInvoiceTypeName').value.trim();

        if (!name) {
            alert('يرجى إدخال اسم النوع');
            document.getElementById('newInvoiceTypeName').focus();
            return;
        }

        btnSaveInvoiceType.disabled = true;
        btnSaveInvoiceType.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

        fetch('{{ route("admin.invoice-types.store") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إضافة النوع الجديد للقائمة
                const select = document.getElementById('invoiceTypeId');
                const option = new Option(data.type.name, data.type.id, true, true);
                select.add(option);

                // إغلاق Modal وتنظيف
                addInvoiceTypeModal.hide();
                document.getElementById('newInvoiceTypeName').value = '';
            } else {
                alert(data.message || 'حدث خطأ');
            }
            btnSaveInvoiceType.disabled = false;
            btnSaveInvoiceType.innerHTML = '<i class="fas fa-save me-1"></i> حفظ';
        })
        .catch(error => {
            btnSaveInvoiceType.disabled = false;
            btnSaveInvoiceType.innerHTML = '<i class="fas fa-save me-1"></i> حفظ';
            alert('حدث خطأ في الاتصال');
            console.error(error);
        });
    });
});

// === تعديل الفاتورة ===
let currentEditInvoiceId = null;
let editInvoiceItems = [];
const editInvoiceModal = new bootstrap.Modal(document.getElementById('editInvoiceModal'));

// دالة حساب وتحديث الإجمالي من العناصر
function calculateEditInvoiceItemsTotal() {
    let total = 0;
    editInvoiceItems.forEach(item => {
        total += (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 1);
    });
    window.currentInvoiceTotal = total;
    document.getElementById('editInvoiceItemsTotal').textContent = total.toFixed(2) + ' د.ل';
    document.getElementById('editInvoiceTotalDisplay').textContent = total.toFixed(2) + ' د.ل';
    updateEditInvoiceBalance();
    return total;
}

// دالة تحديث الملخص المالي عند تغيير المبلغ المدفوع أو الخصم
function updateEditInvoiceBalance() {
    const total = window.currentInvoiceTotal || 0;
    const paid = parseFloat(document.getElementById('editInvoicePaidAmount').value) || 0;
    const discount = parseFloat(document.getElementById('editInvoiceDiscount').value) || 0;
    const balance = total - paid - discount;

    document.getElementById('editInvoicePaidDisplay').textContent = (paid + discount).toFixed(2) + ' د.ل';
    document.getElementById('editInvoiceBalanceDisplay').textContent = balance.toFixed(2) + ' د.ل';
}

// دالة عرض عناصر الفاتورة في الجدول
function renderEditInvoiceItems() {
    const tbody = document.getElementById('editInvoiceItemsBody');
    tbody.innerHTML = '';

    editInvoiceItems.forEach((item, index) => {
        const itemTotal = (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 1);
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center">${index + 1}</td>
            <td>
                <input type="text" class="form-control form-control-sm" value="${item.name || ''}"
                       onchange="updateEditInvoiceItem(${index}, 'name', this.value)" placeholder="اسم البند">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" value="${item.price || 0}" step="0.01" min="0"
                       onchange="updateEditInvoiceItem(${index}, 'price', this.value)" placeholder="0.00">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" value="${item.quantity || 1}" min="1"
                       onchange="updateEditInvoiceItem(${index}, 'quantity', this.value)">
            </td>
            <td class="fw-bold text-center" style="color: #063973;">${itemTotal.toFixed(2)} د.ل</td>
            <td class="text-center">
                ${editInvoiceItems.length > 1 ?
                    `<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEditInvoiceItem(${index})">
                        <i class="fas fa-times"></i>
                    </button>` : ''
                }
            </td>
        `;
        tbody.appendChild(tr);
    });

    calculateEditInvoiceItemsTotal();
}

// دالة تحديث عنصر في الفاتورة
function updateEditInvoiceItem(index, field, value) {
    if (editInvoiceItems[index]) {
        editInvoiceItems[index][field] = value;
        renderEditInvoiceItems();
    }
}

// دالة إضافة عنصر جديد للفاتورة
function addEditInvoiceItem() {
    editInvoiceItems.push({
        name: '',
        price: 0,
        quantity: 1
    });
    renderEditInvoiceItems();
}

// دالة حذف عنصر من الفاتورة
function removeEditInvoiceItem(index) {
    if (editInvoiceItems.length > 1) {
        editInvoiceItems.splice(index, 1);
        renderEditInvoiceItems();
    }
}

// ربط زر إضافة عنصر
document.getElementById('btnAddInvoiceItem').addEventListener('click', addEditInvoiceItem);

// ربط أحداث التغيير على حقول المبلغ المدفوع والخصم
document.getElementById('editInvoicePaidAmount').addEventListener('input', updateEditInvoiceBalance);
document.getElementById('editInvoiceDiscount').addEventListener('input', updateEditInvoiceBalance);

function openEditInvoiceModal(invoiceId) {
    currentEditInvoiceId = invoiceId;
    editInvoiceItems = [];
    document.getElementById('editInvoiceLoading').style.display = 'block';
    document.getElementById('editInvoiceForm').style.display = 'none';

    editInvoiceModal.show();

    // جلب بيانات الفاتورة
    fetch(`{{ url('admin/invoices') }}/${invoiceId}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const invoice = data.invoice;
            document.getElementById('editInvoiceId').value = invoice.id;
            document.getElementById('editInvoiceTypeId').value = invoice.invoice_type_id || '';
            document.getElementById('editInvoiceDescription').value = invoice.description || '';
            document.getElementById('editInvoicePaidAmount').value = invoice.paid_amount || 0;
            document.getElementById('editInvoiceDiscount').value = invoice.discount || 0;
            document.getElementById('editInvoiceNotes').value = invoice.notes || '';

            // تحميل عناصر الفاتورة
            editInvoiceItems = [];
            if (invoice.items && invoice.items.length > 0) {
                invoice.items.forEach(item => {
                    editInvoiceItems.push({
                        id: item.id,
                        assessment_id: item.assessment_id,
                        name: item.assessment_name,
                        price: parseFloat(item.price) || 0,
                        quantity: parseInt(item.quantity) || 1,
                        assessment_status: item.assessment_status,
                        assessment_result: item.assessment_result,
                        assessment_notes: item.assessment_notes
                    });
                });
            } else {
                // إذا لم تكن هناك عناصر، أضف عنصر فارغ
                editInvoiceItems.push({
                    name: invoice.description || '',
                    price: parseFloat(invoice.total_amount) || 0,
                    quantity: 1
                });
            }

            renderEditInvoiceItems();

            document.getElementById('editInvoiceLoading').style.display = 'none';
            document.getElementById('editInvoiceForm').style.display = 'block';
        } else {
            alert(data.message || 'حدث خطأ في تحميل البيانات');
            editInvoiceModal.hide();
        }
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال بالخادم');
        editInvoiceModal.hide();
    });
}

// حفظ تعديلات الفاتورة
document.getElementById('btnUpdateInvoice').addEventListener('click', function() {
    // التحقق من وجود عناصر
    if (editInvoiceItems.length === 0) {
        alert('يجب إضافة عنصر واحد على الأقل');
        return;
    }

    // التحقق من أن كل عنصر له اسم
    for (let i = 0; i < editInvoiceItems.length; i++) {
        if (!editInvoiceItems[i].name || editInvoiceItems[i].name.trim() === '') {
            alert('يرجى إدخال اسم البند رقم ' + (i + 1));
            return;
        }
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

    const formData = {
        invoice_type_id: document.getElementById('editInvoiceTypeId').value,
        description: document.getElementById('editInvoiceDescription').value,
        paid_amount: document.getElementById('editInvoicePaidAmount').value || 0,
        discount: document.getElementById('editInvoiceDiscount').value || 0,
        notes: document.getElementById('editInvoiceNotes').value,
        items: editInvoiceItems
    };

    fetch(`{{ url('admin/invoices') }}/${currentEditInvoiceId}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            localStorage.setItem('student_{{ $student->id }}_active_tab', '#invoices');
            window.location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ التغييرات';
            alert(data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ التغييرات';
        alert('حدث خطأ في الاتصال بالخادم');
    });
});

// حذف الفاتورة
document.getElementById('btnDeleteInvoice').addEventListener('click', function() {
    if (!confirm('هل أنت متأكد من حذف هذه الفاتورة؟')) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    fetch(`{{ url('admin/invoices') }}/${currentEditInvoiceId}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            localStorage.setItem('student_{{ $student->id }}_active_tab', '#invoices');
            window.location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
            alert(data.message || 'حدث خطأ أثناء الحذف');
        }
    })
    .catch(error => {
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
        alert('حدث خطأ في الاتصال بالخادم');
    });
});

// === الجلسات الفردية ===
let previewedSessions = [];
let sessionPricePerUnit = 0;
let deletePackageId = null;

const dayNames = {
    'saturday': 'السبت',
    'sunday': 'الأحد',
    'monday': 'الإثنين',
    'tuesday': 'الثلاثاء',
    'wednesday': 'الأربعاء',
    'thursday': 'الخميس',
    'friday': 'الجمعة'
};

// معاينة الجلسات
document.getElementById('btnPreviewSessions').addEventListener('click', function() {
    const therapySessionId = document.getElementById('therapySessionId').value;
    const specialistId = document.getElementById('specialistId').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const sessionTime = document.getElementById('sessionTime').value;
    const selectedDays = Array.from(document.querySelectorAll('.day-checkbox:checked')).map(cb => cb.value);

    // التحقق من البيانات
    if (!therapySessionId) {
        alert('يرجى اختيار نوع الجلسة');
        return;
    }
    if (!specialistId) {
        alert('يرجى اختيار الأخصائي');
        return;
    }
    if (!startDate || !endDate) {
        alert('يرجى تحديد الفترة الزمنية');
        return;
    }
    if (!sessionTime) {
        alert('يرجى تحديد وقت الجلسة');
        return;
    }
    if (selectedDays.length === 0) {
        alert('يرجى اختيار يوم واحد على الأقل');
        return;
    }

    // الحصول على سعر الجلسة
    const selectedOption = document.getElementById('therapySessionId').selectedOptions[0];
    sessionPricePerUnit = parseFloat(selectedOption.dataset.price) || 0;

    // عرض التحميل
    document.getElementById('noPreviewMessage').style.display = 'none';
    document.getElementById('sessionsPreviewLoading').style.display = 'block';
    document.getElementById('sessionsPreviewTable').style.display = 'none';

    // طلب المعاينة من الخادم
    fetch('{{ route("admin.session-packages.preview") }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            start_date: startDate,
            end_date: endDate,
            session_time: sessionTime,
            days: selectedDays
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('sessionsPreviewLoading').style.display = 'none';

        if (data.success) {
            previewedSessions = data.sessions;
            renderPreviewTable();
            document.getElementById('sessionsPreviewTable').style.display = 'block';
            document.getElementById('btnSaveSessionPackage').disabled = false;
        } else {
            alert(data.message || 'حدث خطأ');
            document.getElementById('noPreviewMessage').style.display = 'block';
        }
    })
    .catch(error => {
        console.error(error);
        document.getElementById('sessionsPreviewLoading').style.display = 'none';
        document.getElementById('noPreviewMessage').style.display = 'block';
        alert('حدث خطأ في الاتصال');
    });
});

// عرض جدول المعاينة
function renderPreviewTable() {
    const tbody = document.getElementById('sessionsPreviewBody');
    tbody.innerHTML = '';

    previewedSessions.forEach((session, index) => {
        const tr = document.createElement('tr');
        tr.id = `preview-row-${index}`;
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${dayNames[session.day_name] || session.day_name}</td>
            <td>${session.date}</td>
            <td>${formatTime(session.time)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePreviewSession(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    updatePreviewSummary();
}

// إزالة جلسة من المعاينة
function removePreviewSession(index) {
    previewedSessions.splice(index, 1);
    renderPreviewTable();

    if (previewedSessions.length === 0) {
        document.getElementById('sessionsPreviewTable').style.display = 'none';
        document.getElementById('noPreviewMessage').style.display = 'block';
        document.getElementById('btnSaveSessionPackage').disabled = true;
    }
}

// تحديث ملخص المعاينة
function updatePreviewSummary() {
    const count = previewedSessions.length;
    const total = count * sessionPricePerUnit;
    document.getElementById('sessionsCount').textContent = count;
    document.getElementById('totalPrice').textContent = total.toFixed(2) + ' د.ل';
}

// تحويل الوقت للتنسيق المقروء
function formatTime(time) {
    const [hours, minutes] = time.split(':');
    const h = parseInt(hours);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12 = h % 12 || 12;
    return `${h12}:${minutes} ${ampm}`;
}

// حفظ الباقة
document.getElementById('btnSaveSessionPackage').addEventListener('click', function() {
    if (previewedSessions.length === 0) {
        alert('لا توجد جلسات للحفظ');
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

    const formData = {
        therapy_session_id: document.getElementById('therapySessionId').value,
        specialist_id: document.getElementById('specialistId').value,
        start_date: document.getElementById('startDate').value,
        end_date: document.getElementById('endDate').value,
        session_time: document.getElementById('sessionTime').value,
        session_duration: document.getElementById('sessionDuration').value,
        days: Array.from(document.querySelectorAll('.day-checkbox:checked')).map(cb => cb.value),
        notes: document.getElementById('packageNotes').value,
        sessions: previewedSessions
    };

    fetch('{{ route("admin.session-packages.store", $student) }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            localStorage.setItem('student_{{ $student->id }}_active_tab', '#sessions');
            window.location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ الباقة';
            alert(data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ الباقة';
        alert('حدث خطأ في الاتصال');
    });
});

// إعادة تعيين Modal عند الإغلاق
document.getElementById('createSessionPackageModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('sessionPackageForm').reset();
    previewedSessions = [];
    document.getElementById('noPreviewMessage').style.display = 'block';
    document.getElementById('sessionsPreviewLoading').style.display = 'none';
    document.getElementById('sessionsPreviewTable').style.display = 'none';
    document.getElementById('btnSaveSessionPackage').disabled = true;
    document.getElementById('btnSaveSessionPackage').innerHTML = '<i class="fas fa-save me-1"></i> حفظ الباقة';
});

// حذف باقة
function deletePackage(id) {
    deletePackageId = id;
    new bootstrap.Modal(document.getElementById('deletePackageModal')).show();
}

document.getElementById('btnConfirmDeletePackage').addEventListener('click', function() {
    if (!deletePackageId) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

    fetch(`{{ url('admin/session-packages') }}/${deletePackageId}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            localStorage.setItem('student_{{ $student->id }}_active_tab', '#sessions');
            window.location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
        alert('حدث خطأ في الاتصال');
    });
});
</script>
@endpush

@if($student->status === 'new')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCaseStudy = document.getElementById('btnCaseStudy');
    const caseStudyModal = new bootstrap.Modal(document.getElementById('caseStudyModal'));
    const btnSaveCaseStudy = document.getElementById('btnSaveCaseStudy');

    let caseStudyPrice = 0;
    let selectedAssessments = {};

    // فتح Modal عند الضغط على الزر
    btnCaseStudy.addEventListener('click', function() {
        caseStudyModal.show();
        loadCaseStudyData();
    });

    // تحميل بيانات دراسة الحالة
    function loadCaseStudyData() {
        document.getElementById('caseStudyLoading').style.display = 'block';
        document.getElementById('caseStudyContent').style.display = 'none';
        document.getElementById('caseStudyError').style.display = 'none';
        btnSaveCaseStudy.disabled = true;

        fetch('{{ route("admin.students.case.create", $student) }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // عرض دراسة الحالة الأساسية
                if (data.caseStudy) {
                    document.getElementById('caseStudyName').textContent = data.caseStudy.name;
                    document.getElementById('caseStudyPrice').textContent = parseFloat(data.caseStudy.price).toFixed(2);
                    caseStudyPrice = parseFloat(data.caseStudy.price);
                }

                // عرض المقاييس الإضافية
                let assessmentsHtml = '';
                if (data.assessments && data.assessments.length > 0) {
                    data.assessments.forEach(function(assessment, index) {
                        assessmentsHtml += `
                            <div class="assessment-item p-3 ${index < data.assessments.length - 1 ? 'border-bottom' : ''}" style="transition: background 0.2s;">
                                <div class="form-check d-flex align-items-center m-0">
                                    <input class="form-check-input assessment-checkbox me-3" type="checkbox"
                                           id="assessment_${assessment.id}"
                                           name="assessments[]"
                                           value="${assessment.id}"
                                           data-price="${assessment.price}"
                                           style="width: 1.2em; height: 1.2em;">
                                    <label class="form-check-label d-flex justify-content-between align-items-center w-100 cursor-pointer" for="assessment_${assessment.id}">
                                        <div>
                                            <span class="fw-medium">${assessment.name}</span>
                                        </div>
                                        <span class="badge bg-light text-dark border px-3 py-2">
                                            ${parseFloat(assessment.price).toFixed(2)} <small>د.ل</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    assessmentsHtml = `
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">لا توجد مقاييس إضافية</p>
                        </div>
                    `;
                }
                document.getElementById('assessmentsList').innerHTML = assessmentsHtml;

                // ربط حدث التغيير بالـ checkboxes
                document.querySelectorAll('.assessment-checkbox').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        const item = this.closest('.assessment-item');
                        if (this.checked) {
                            item.style.background = '#e8f4fd';
                        } else {
                            item.style.background = '';
                        }
                        updateTotal();
                    });
                });

                // حساب المجموع الأولي
                updateTotal();

                document.getElementById('caseStudyLoading').style.display = 'none';
                document.getElementById('caseStudyContent').style.display = 'block';
                btnSaveCaseStudy.disabled = false;
            } else {
                showError(data.message || 'حدث خطأ أثناء تحميل البيانات');
            }
        })
        .catch(error => {
            showError('حدث خطأ في الاتصال بالخادم');
            console.error(error);
        });
    }

    // تحديث المجموع
    function updateTotal() {
        let total = caseStudyPrice;
        let selectedCount = 0;
        document.querySelectorAll('.assessment-checkbox:checked').forEach(function(checkbox) {
            total += parseFloat(checkbox.dataset.price);
            selectedCount++;
        });
        document.getElementById('totalAmount').textContent = total.toFixed(2);
    }

    // عرض رسالة الخطأ
    function showError(message) {
        document.getElementById('caseStudyLoading').style.display = 'none';
        document.getElementById('caseStudyContent').style.display = 'none';
        document.getElementById('caseStudyError').style.display = 'block';
        document.getElementById('errorMessage').textContent = message;
    }

    // حفظ دراسة الحالة
    btnSaveCaseStudy.addEventListener('click', function() {
        btnSaveCaseStudy.disabled = true;
        btnSaveCaseStudy.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

        const formData = new FormData(document.getElementById('caseStudyForm'));

        fetch('{{ route("admin.students.case.store", $student) }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // إغلاق Modal وإعادة تحميل الصفحة
                caseStudyModal.hide();
                window.location.reload();
            } else {
                btnSaveCaseStudy.disabled = false;
                btnSaveCaseStudy.innerHTML = '<i class="fas fa-save me-1"></i> حفظ وإنشاء الفاتورة';
                alert(data.message || 'حدث خطأ أثناء الحفظ');
            }
        })
        .catch(error => {
            btnSaveCaseStudy.disabled = false;
            btnSaveCaseStudy.innerHTML = '<i class="fas fa-save me-1"></i> حفظ وإنشاء الفاتورة';
            alert('حدث خطأ في الاتصال بالخادم');
            console.error(error);
        });
    });
});
</script>
@endpush
@endif

@if($student->currentCase)
@push('scripts')
<script>
let assessmentModal;
let currentItemId = null;

document.addEventListener('DOMContentLoaded', function() {
    assessmentModal = new bootstrap.Modal(document.getElementById('assessmentModal'));
});

function openAssessmentModal(itemId) {
    currentItemId = itemId;
    document.getElementById('assessmentLoading').style.display = 'block';
    document.getElementById('assessmentFormContainer').style.display = 'none';
    document.getElementById('btnSaveAssessment').disabled = true;

    assessmentModal.show();

    fetch(`{{ url('admin/assessment-results') }}/${itemId}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('assessmentModalTitle').textContent = data.item.assessment_name;
            document.getElementById('assessmentItemId').value = itemId;
            document.getElementById('assessmentResult').value = data.item.assessment_result || '';
            document.getElementById('assessmentNotes').value = data.item.assessment_notes || '';

            document.getElementById('assessmentLoading').style.display = 'none';
            document.getElementById('assessmentFormContainer').style.display = 'block';
            document.getElementById('btnSaveAssessment').disabled = false;
        } else {
            alert(data.message || 'حدث خطأ في تحميل البيانات');
            assessmentModal.hide();
        }
    })
    .catch(error => {
        console.error(error);
        alert('حدث خطأ في الاتصال بالخادم');
        assessmentModal.hide();
    });
}

function saveAssessment() {
    const resultField = document.getElementById('assessmentResult');
    if (!resultField.value.trim()) {
        alert('يرجى إدخال نتيجة التقييم');
        resultField.focus();
        return;
    }

    const btn = document.getElementById('btnSaveAssessment');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

    const formData = new FormData();
    formData.append('assessment_result', document.getElementById('assessmentResult').value);
    formData.append('assessment_notes', document.getElementById('assessmentNotes').value);
    formData.append('_method', 'PUT');

    fetch(`{{ url('admin/assessment-results') }}/${currentItemId}`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            assessmentModal.hide();

            // إذا اكتملت جميع التقييمات، أعد تحميل الصفحة
            if (data.all_completed) {
                window.location.reload();
            } else {
                // تحديث العنصر في الصفحة
                window.location.reload();
            }
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ التقييم';
            alert(data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ التقييم';
        alert('حدث خطأ في الاتصال بالخادم');
        console.error(error);
    });
}

// ========== Excused Absence Functions ==========
let excusedAbsenceModal;

function toggleReasonDetails(select) {
    const wrapper = document.getElementById('reasonDetailsWrapper');
    const textarea = wrapper.querySelector('textarea');
    if (select.value === 'other') {
        wrapper.style.display = 'block';
        textarea.required = true;
    } else {
        wrapper.style.display = 'none';
        textarea.required = false;
        textarea.value = '';
    }
}

function openExcusedAbsenceModal() {
    // إغلاق modal القائمة إذا كان مفتوحاً
    const listModal = bootstrap.Modal.getInstance(document.getElementById('excusedAbsenceListModal'));
    if (listModal) listModal.hide();

    // إعادة تعيين النموذج
    document.getElementById('excusedAbsenceForm').reset();
    document.getElementById('reasonDetailsWrapper').style.display = 'none';

    // فتح modal الإضافة
    excusedAbsenceModal = new bootstrap.Modal(document.getElementById('excusedAbsenceModal'));
    excusedAbsenceModal.show();
}

function openExcusedAbsenceList() {
    const modal = new bootstrap.Modal(document.getElementById('excusedAbsenceListModal'));
    modal.show();
    loadExcusedAbsenceList();
}

function loadExcusedAbsenceList() {
    const content = document.getElementById('excusedAbsenceListContent');
    content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>';

    fetch('{{ route("admin.excused-absences.index", $student) }}', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.excusedAbsences.length === 0) {
                content.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-3x mb-3"></i><p>لا توجد سجلات غياب بإذن</p></div>';
            } else {
                let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
                html += '<thead><tr><th>النوع</th><th>الفترة</th><th>السبب</th><th>بواسطة</th><th></th></tr></thead><tbody>';
                data.excusedAbsences.forEach(item => {
                    const typeText = item.type === 'sessions' ? 'جلسات' : 'رعاية نهارية';
                    const reasonTexts = {illness: 'مرض', travel: 'سفر', family: 'ظرف عائلي', other: 'سبب آخر'};
                    html += `<tr>
                        <td><span class="badge bg-${item.type === 'sessions' ? 'primary' : 'warning'}">${typeText}</span></td>
                        <td>${item.start_date} إلى ${item.end_date}</td>
                        <td>${reasonTexts[item.reason] || item.reason}${item.reason === 'other' && item.reason_details ? ': ' + item.reason_details : ''}</td>
                        <td><small class="text-muted">${item.creator ? item.creator.name : '-'}</small></td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteExcusedAbsence(${item.id})"><i class="fas fa-trash"></i></button></td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                content.innerHTML = html;
            }
        } else {
            content.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>';
        }
    })
    .catch(error => {
        console.error(error);
        content.innerHTML = '<div class="alert alert-danger">حدث خطأ في الاتصال</div>';
    });
}

document.getElementById('excusedAbsenceForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const btn = document.getElementById('btnSaveExcusedAbsence');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري الحفظ...';

    const formData = new FormData(this);

    fetch('{{ route("admin.excused-absences.store", $student) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            excusedAbsenceModal.hide();
            location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ';
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ';
        alert('حدث خطأ في الاتصال');
        console.error(error);
    });
});

function deleteExcusedAbsence(id) {
    if (!confirm('هل أنت متأكد من حذف سجل الغياب بإذن هذا؟')) return;

    fetch(`/admin/excused-absences/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        alert('حدث خطأ في الاتصال');
        console.error(error);
    });
}
</script>
@endpush
@endif

@if($student->status !== 'new')
@push('scripts')
<script>
// ========== Daycare Functions ==========
let deleteDaycareId = null;
let cancelDaycareId = null;

// Submit daycare form
document.getElementById('daycareForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري الحفظ...';

    fetch('{{ route("admin.daycare.store", $student) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ الاشتراك';
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> حفظ الاشتراك';
        // Handle validation errors
        if (error.errors) {
            let errorMessages = [];
            for (let field in error.errors) {
                errorMessages.push(error.errors[field].join('\n'));
            }
            alert(errorMessages.join('\n'));
        } else if (error.message) {
            alert(error.message);
        } else {
            alert('حدث خطأ في الاتصال');
        }
        console.error('Error:', error);
    });
});

// Delete daycare
function deleteDaycare(id) {
    deleteDaycareId = id;
    new bootstrap.Modal(document.getElementById('deleteDaycareModal')).show();
}

document.getElementById('btnConfirmDeleteDaycare')?.addEventListener('click', function() {
    if (!deleteDaycareId) return;

    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري الحذف...';

    fetch(`/admin/daycare/${deleteDaycareId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-trash me-1"></i> حذف';
        alert('حدث خطأ في الاتصال');
        console.error(error);
    });
});

// Cancel daycare
function cancelDaycare(id) {
    cancelDaycareId = id;
    new bootstrap.Modal(document.getElementById('cancelDaycareModal')).show();
}

document.getElementById('btnConfirmCancelDaycare')?.addEventListener('click', function() {
    if (!cancelDaycareId) return;

    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري الإلغاء...';

    fetch(`/admin/daycare/${cancelDaycareId}/cancel`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-ban me-1"></i> إلغاء الاشتراك';
            alert(data.message || 'حدث خطأ');
        }
    })
    .catch(error => {
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-ban me-1"></i> إلغاء الاشتراك';
        alert('حدث خطأ في الاتصال');
        console.error(error);
    });
});
</script>
@endpush
@endif
