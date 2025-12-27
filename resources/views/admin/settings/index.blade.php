@extends('layouts.app')

@section('title', 'إعدادات النظام')

@section('content')

        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- إعدادات الدوام -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="ti ti-clock me-2"></i>
                                إعدادات الدوام
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">وقت بداية الدوام</label>
                                        <input type="time" name="work_start_time" class="form-control @error('work_start_time') is-invalid @enderror"
                                            value="{{ old('work_start_time', \App\Models\Setting::get('work_start_time', '08:30')) }}">
                                        @error('work_start_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">وقت نهاية الدوام</label>
                                        <input type="time" name="work_end_time" class="form-control @error('work_end_time') is-invalid @enderror"
                                            value="{{ old('work_end_time', \App\Models\Setting::get('work_end_time', '12:30')) }}">
                                        @error('work_end_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- إعدادات الحوافز -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="ti ti-gift me-2"></i>
                                إعدادات الحوافز
                            </h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="incentives_enabled" id="incentives_enabled" value="1"
                                    {{ old('incentives_enabled', \App\Models\Setting::get('incentives_enabled', '1')) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="incentives_enabled">تفعيل نظام الحوافز</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning mb-3">
                                <i class="ti ti-clock me-2"></i>
                                <strong>مهم:</strong> الحوافز تُضاف فقط للعمل الذي يتم <strong>بعد انتهاء وقت الدوام الرسمي</strong> (بعد {{ \App\Models\Setting::get('work_end_time', '12:30') }}).
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">قيمة حافز الجلسة (د.ل)</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" name="session_incentive_amount"
                                                class="form-control @error('session_incentive_amount') is-invalid @enderror"
                                                value="{{ old('session_incentive_amount', \App\Models\Setting::get('session_incentive_amount', '10.00')) }}">
                                            <span class="input-group-text">د.ل</span>
                                            @error('session_incentive_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">المبلغ الذي يضاف للموظف عند إكمال جلسة</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">قيمة حافز الرعاية النهارية (د.ل)</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" name="daycare_incentive_amount"
                                                class="form-control @error('daycare_incentive_amount') is-invalid @enderror"
                                                value="{{ old('daycare_incentive_amount', \App\Models\Setting::get('daycare_incentive_amount', '5.00')) }}">
                                            <span class="input-group-text">د.ل</span>
                                            @error('daycare_incentive_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">المبلغ الذي يضاف للموظف عند تسجيل حضور رعاية</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-2"></i>
                                حفظ الإعدادات
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- معلومات جانبية -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            معلومات
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-primary mb-2">نظام الحوافز</h6>
                        <p class="text-muted small mb-3">
                            نظام الحوافز يضيف مبالغ تلقائية لحسابات الموظفين عند العمل <strong>بعد الدوام</strong>:
                        </p>
                        <ul class="list-unstyled small text-muted">
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                إكمال جلسة علاجية (بعد الدوام)
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                تسجيل حضور رعاية نهارية (بعد الدوام)
                            </li>
                        </ul>

                        <hr>

                        <h6 class="text-primary mb-2">شروط الحافز</h6>
                        <ul class="list-unstyled small text-muted">
                            <li class="mb-2">
                                <i class="ti ti-clock text-info me-2"></i>
                                يجب أن يكون الوقت بعد نهاية الدوام
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-alert-circle text-warning me-2"></i>
                                الحوافز تضاف مرة واحدة فقط لكل عملية
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-toggle-right text-warning me-2"></i>
                                يمكن إيقاف النظام مؤقتاً من الزر أعلاه
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
@endsection
