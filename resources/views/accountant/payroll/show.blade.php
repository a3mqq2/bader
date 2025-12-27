@extends('layouts.app')

@section('title', 'كشف مرتبات ' . $payroll->period_text)

@section('content')
<div class="row">
    <!-- اختيار الفترة + الحالة -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('accountant.payroll.show') }}" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <span class="badge bg-{{ $payroll->status === 'executed' ? 'success' : 'warning' }} fs-6 px-3 py-2">
                            <i class="ti ti-{{ $payroll->status === 'executed' ? 'check' : 'clock' }} me-1"></i>
                            {{ $payroll->status_text }}
                        </span>
                    </div>
                    <div class="col-md-2">
                        <select name="year" class="form-select">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="month" class="form-select">
                            @foreach($months as $m => $name)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> عرض
                        </button>
                    </div>
                    <div class="col-auto ms-auto">
                        @if($payroll->status === 'executed')
                            <a href="{{ route('accountant.payroll.print', $payroll) }}" target="_blank" class="btn btn-outline-secondary">
                                <i class="ti ti-printer me-1"></i> طباعة
                            </a>
                        @endif
                        <a href="{{ route('accountant.payroll.index') }}" class="btn btn-outline-primary">
                            <i class="ti ti-arrow-right me-1"></i> رجوع للقائمة
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ملخص الكشف -->
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body py-4">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <div class="border-end border-white border-opacity-25">
                            <i class="ti ti-users fs-2 opacity-75 mb-2 d-block"></i>
                            <h3 class="mb-0 fw-bold text-white">{{ $payroll->items->count() }}</h3>
                            <small class="opacity-75">عدد الموظفين</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <div class="border-end border-white border-opacity-25">
                            <i class="ti ti-wallet fs-2 opacity-75 mb-2 d-block"></i>
                            <h3 class="mb-0 fw-bold text-white">{{ number_format($payroll->total_salaries, 2) }}</h3>
                            <small class="opacity-75">إجمالي الرواتب</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="border-end border-white border-opacity-25">
                            <div class="d-flex justify-content-center gap-4">
                                <div>
                                    <span class="badge bg-success fs-6 text-white mb-1">+{{ number_format($payroll->total_bonuses, 2) }}</span>
                                    <small class="d-block opacity-75">مكافآت</small>
                                </div>
                                <div>
                                    <span class="badge bg-danger text-white fs-6 mb-1">-{{ number_format($payroll->total_deductions, 2) }}</span>
                                    <small class="d-block opacity-75">خصومات</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <i class="ti ti-cash fs-2 opacity-75 mb-2 d-block"></i>
                        <h2 class="mb-0 fw-bold text-white">{{ number_format($payroll->total_net, 2) }}</h2>
                        <small class="opacity-75">صافي الصرف (د.ل)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تنفيذ الكشف (في الأعلى للوضوح) -->
    @if($payroll->status === 'draft')
        <div class="col-12 mb-4">
            <div class="alert alert-warning border-warning mb-0">
                <form action="{{ route('accountant.payroll.execute', $payroll) }}" method="POST" class="row g-3 align-items-center" onsubmit="return confirm('هل أنت متأكد من تنفيذ كشف المرتبات؟\n\nسيتم تسجيل الرواتب في حسابات الموظفين.\n(لا يتم الخصم من الخزينة - الصرف الفعلي يكون لاحقاً)\n\nهذا الإجراء لا يمكن التراجع عنه.')">
                    @csrf
                    <div class="col-auto">
                        <i class="ti ti-alert-triangle fs-3 text-warning"></i>
                    </div>
                    <div class="col">
                        <strong>الكشف جاهز للتنفيذ</strong>
                        <div class="small text-muted">راجع بيانات الموظفين ثم اضغط على تنفيذ لتسجيل الرواتب في حساباتهم</div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="ti ti-player-play me-1"></i> تنفيذ التسجيل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- جدول الموظفين -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="ti ti-list me-2"></i>
                    تفاصيل رواتب الموظفين
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">الموظف</th>
                                <th style="width: 12%" class="text-center">الحضور</th>
                                <th style="width: 15%" class="text-center">الراتب الأساسي</th>
                                <th style="width: 12%" class="text-center text-success">المكافأة</th>
                                <th style="width: 12%" class="text-center text-danger">الخصم</th>
                                <th style="width: 15%" class="text-center">الصافي</th>
                                <th style="width: 9%" class="text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payroll->items as $index => $item)
                                <tr class="{{ $item->is_processed ? 'table-success' : '' }}">
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avtar avtar-xs bg-light-primary">
                                                <i class="ti ti-user text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $item->user->name }}</div>
                                                <small class="text-muted">{{ $item->user->code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-primary text-primary">{{ $item->work_days }} يوم</span>
                                        <div class="small text-muted">{{ number_format($item->work_hours, 1) }} ساعة</div>
                                    </td>
                                    <td class="text-center">
                                        @if($payroll->status === 'draft')
                                            <input type="number" class="form-control form-control-sm text-center payroll-input"
                                                data-item-id="{{ $item->id }}" data-field="base_salary"
                                                value="{{ $item->base_salary }}" step="0.01" min="0" style="width: 100px; margin: auto;">
                                        @else
                                            <strong>{{ number_format($item->base_salary, 2) }}</strong>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($payroll->status === 'draft')
                                            <div class="input-group input-group-sm" style="width: 100px; margin: auto;">
                                                <span class="input-group-text bg-success text-white p-1">+</span>
                                                <input type="number" class="form-control text-center payroll-input"
                                                    data-item-id="{{ $item->id }}" data-field="bonus"
                                                    value="{{ $item->bonus }}" step="0.01" min="0">
                                            </div>
                                        @else
                                            @if($item->bonus > 0)
                                                <span class="text-success fw-bold">+{{ number_format($item->bonus, 2) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($payroll->status === 'draft')
                                            <div class="input-group input-group-sm" style="width: 100px; margin: auto;">
                                                <span class="input-group-text bg-danger text-white p-1">-</span>
                                                <input type="number" class="form-control text-center payroll-input"
                                                    data-item-id="{{ $item->id }}" data-field="deduction"
                                                    value="{{ $item->deduction }}" step="0.01" min="0">
                                            </div>
                                        @else
                                            @if($item->deduction > 0)
                                                <span class="text-danger fw-bold">-{{ number_format($item->deduction, 2) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <strong class="fs-6 text-primary net-salary-{{ $item->id }}">{{ number_format($item->net_salary, 2) }}</strong>
                                        <small class="d-block text-muted">د.ل</small>
                                    </td>
                                    <td class="text-center">
                                        @if($item->is_processed)
                                            <span class="badge bg-success"><i class="ti ti-check"></i> تم</span>
                                        @else
                                            <span class="badge bg-secondary">قيد الانتظار</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($payroll->status === 'draft')
                                    <tr class="bg-light">
                                        <td></td>
                                        <td colspan="7" class="py-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-note text-muted"></i>
                                                <input type="text" class="form-control form-control-sm border-0 bg-transparent payroll-input"
                                                    data-item-id="{{ $item->id }}" data-field="notes"
                                                    value="{{ $item->notes }}" placeholder="ملاحظات (اختياري) - مثال: سبب المكافأة أو الخصم..."
                                                    style="max-width: 500px;">
                                            </div>
                                        </td>
                                    </tr>
                                @elseif($item->notes)
                                    <tr class="bg-light">
                                        <td></td>
                                        <td colspan="7" class="py-2 text-muted small">
                                            <i class="ti ti-note me-1"></i> {{ $item->notes }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="table-primary">
                            <tr>
                                <th colspan="3" class="text-center">الإجمالي</th>
                                <th class="text-center">{{ number_format($payroll->total_salaries, 2) }}</th>
                                <th class="text-center text-success">+{{ number_format($payroll->total_bonuses, 2) }}</th>
                                <th class="text-center text-danger">-{{ number_format($payroll->total_deductions, 2) }}</th>
                                <th class="text-center fs-5 text-primary">{{ number_format($payroll->total_net, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($payroll->status === 'draft')
<!-- مؤشر الحفظ -->
<div id="saveIndicator" class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="display: none; z-index: 1050;">
    <div class="alert alert-info mb-0 shadow d-flex align-items-center gap-2 py-2 px-3">
        <div class="spinner-border spinner-border-sm text-info" role="status"></div>
        <span>جاري الحفظ...</span>
    </div>
</div>
<div id="savedIndicator" class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="display: none; z-index: 1050;">
    <div class="alert alert-success mb-0 shadow d-flex align-items-center gap-2 py-2 px-3">
        <i class="ti ti-check"></i>
        <span>تم الحفظ</span>
    </div>
</div>
@endif
@endsection

@push('scripts')
@if($payroll->status === 'draft')
<script>
    let saveTimeout;
    const saveIndicator = document.getElementById('saveIndicator');
    const savedIndicator = document.getElementById('savedIndicator');

    document.querySelectorAll('.payroll-input').forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const field = this.dataset.field;
            const value = this.value;

            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                saveField(itemId, field, value);
            }, 300);
        });
    });

    function saveField(itemId, field, value) {
        // إظهار مؤشر الحفظ
        saveIndicator.style.display = 'block';
        savedIndicator.style.display = 'none';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        formData.append(field, value);

        fetch(`{{ url('accountant/payroll/item') }}/${itemId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            saveIndicator.style.display = 'none';
            if (data.success) {
                // تحديث الصافي
                document.querySelector('.net-salary-' + itemId).textContent = data.net_salary;

                // إظهار مؤشر الحفظ الناجح
                savedIndicator.style.display = 'block';
                setTimeout(() => {
                    savedIndicator.style.display = 'none';
                }, 2000);

                // تحديث الصفحة لتحديث الإجماليات
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            saveIndicator.style.display = 'none';
            console.error('Error:', error);
            alert('حدث خطأ أثناء الحفظ');
        });
    }
</script>
@endif
@endpush
