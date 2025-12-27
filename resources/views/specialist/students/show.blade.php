@extends('layouts.app')

@section('title', 'ملف الطالب')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $student->name }}</h5>
                        <span class="badge bg-secondary me-2">{{ $student->code }}</span>
                        <span class="badge bg-{{ $student->status_color }}">{{ $student->status_text }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('specialist.sessions.index', ['search' => $student->code]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-calendar-check me-1"></i> جلسات الطالب
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> رجوع
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- البيانات الشخصية -->
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #063973 0%, #0a5299 100%);">
                        <h6 class="mb-0 text-white">
                            <i class="fas fa-user-graduate me-2"></i>
                            معلومات الطالب
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" width="35%"><i class="fas fa-signature me-2"></i> الاسم</td>
                                    <td class="fw-semibold">{{ $student->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-barcode me-2"></i> الكود</td>
                                    <td>{{ $student->code }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-calendar-alt me-2"></i> تاريخ الميلاد</td>
                                    <td>{{ $student->birth_date->format('Y/m/d') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-hourglass-half me-2"></i> العمر</td>
                                    <td>{{ $student->age }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-{{ $student->gender === 'male' ? 'mars' : 'venus' }} me-2"></i> الجنس</td>
                                    <td>{{ $student->gender_text }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-toggle-on me-2"></i> الحالة</td>
                                    <td><span class="badge bg-{{ $student->status_color }}">{{ $student->status_text }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- معلومات ولي الأمر -->
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #063973 0%, #0a5299 100%);">
                        <h6 class="mb-0 text-white">
                            <i class="fas fa-user-friends me-2"></i>
                            معلومات ولي الأمر
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" width="35%"><i class="fas fa-user me-2"></i> الاسم</td>
                                    <td class="fw-semibold">{{ $student->guardian_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="fas fa-phone me-2"></i> الهاتف</td>
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
                                    <td class="text-muted"><i class="fas fa-phone-alt me-2"></i> هاتف بديل</td>
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
                                    <td class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> العنوان</td>
                                    <td>{{ $student->address }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- دراسة الحالة -->
            @if($student->currentCase)
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header py-3" style="background: linear-gradient(135deg, #063973 0%, #0a5299 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-white">
                                <i class="fas fa-clipboard-check me-2"></i>
                                دراسة الحالة
                            </h6>
                            <span class="badge bg-{{ $student->currentCase->status_color }}">{{ $student->currentCase->status_text }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span class="text-muted">تاريخ الدراسة:</span>
                                    <strong class="ms-2">{{ $student->currentCase->created_at->format('Y/m/d') }}</strong>
                                </div>
                                @if($student->currentCase->creator)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user text-muted me-2"></i>
                                    <span class="text-muted">بواسطة:</span>
                                    <strong class="ms-2">{{ $student->currentCase->creator->name }}</strong>
                                </div>
                                @endif
                            </div>
                            @if($student->currentCase->notes)
                            <div class="col-md-8">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="text-muted mb-2"><i class="fas fa-sticky-note me-1"></i> ملاحظات دراسة الحالة:</h6>
                                    <p class="mb-0">{{ $student->currentCase->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- ملاحظات الطالب -->
            @if($student->notes)
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header py-3 bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note text-primary me-2"></i>
                            ملاحظات عامة
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $student->notes }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- الجلسات الأخيرة مع هذا الأخصائي -->
            @if($sessions->count() > 0)
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header py-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-check text-primary me-2"></i>
                                آخر الجلسات معك
                            </h6>
                            <a href="{{ route('specialist.sessions.index', ['search' => $student->code]) }}" class="btn btn-sm btn-outline-primary">
                                عرض الكل
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الوقت</th>
                                        <th>نوع الجلسة</th>
                                        <th>الحالة</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $session->session_date->format('Y/m/d') }}</span>
                                            <br>
                                            <small class="text-muted">{{ $session->day_name }}</small>
                                        </td>
                                        <td>{{ $session->formatted_time }}</td>
                                        <td>{{ $session->package->therapySession->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $session->status_color }}">{{ $session->status_text }}</span>
                                        </td>
                                        <td>
                                            @if($session->notes)
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $session->notes }}">
                                                {{ Str::limit($session->notes, 50) }}
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- باقات الجلسات النشطة -->
            @if($student->sessionPackages->count() > 0)
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header py-3 bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-box text-primary me-2"></i>
                            باقات الجلسات
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($student->sessionPackages as $package)
                            <div class="col-md-6 col-lg-4">
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">{{ $package->therapySession->name ?? 'جلسة' }}</h6>
                                        <span class="badge bg-{{ $package->status === 'active' ? 'success' : ($package->status === 'completed' ? 'secondary' : 'warning') }}">
                                            {{ $package->status === 'active' ? 'نشطة' : ($package->status === 'completed' ? 'مكتملة' : 'معلقة') }}
                                        </span>
                                    </div>
                                    <div class="small text-muted">
                                        <div class="mb-1">
                                            <i class="fas fa-hashtag me-1"></i>
                                            عدد الجلسات: {{ $package->sessions_count }}
                                        </div>
                                        <div class="mb-1">
                                            <i class="fas fa-clock me-1"></i>
                                            المدة: {{ $package->duration ?? 30 }} دقيقة
                                        </div>
                                        @if($package->days)
                                        <div>
                                            <i class="fas fa-calendar-week me-1"></i>
                                            الأيام: {{ is_array($package->days) ? implode('، ', array_map(fn($d) => \App\Models\SessionPackage::$dayNames[$d] ?? $d, $package->days)) : $package->days }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
