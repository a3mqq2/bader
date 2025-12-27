@extends('layouts.app')

@section('title', 'الرئيسية')

@push('styles')
<style>
    #cameraContainer {
        position: relative;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
    }
    #cameraVideo {
        width: 100%;
        display: block;
    }
    #cameraScanLine {
        position: absolute;
        top: 50%;
        left: 10%;
        right: 10%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #00ff00, transparent);
        animation: scanLine 2s ease-in-out infinite;
    }
    @keyframes scanLine {
        0%, 100% { top: 30%; opacity: 0.5; }
        50% { top: 70%; opacity: 1; }
    }
    #cameraOverlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 3px solid rgba(6, 57, 115, 0.5);
        border-radius: 12px;
    }
    #cameraOverlay::before {
        content: '';
        position: absolute;
        top: 20%;
        left: 15%;
        right: 15%;
        bottom: 20%;
        border: 2px solid #063973;
        border-radius: 8px;
    }
    .input-method-btn {
        border: 2px solid #e9ecef;
        background: #fff;
        transition: all 0.3s ease;
    }
    .input-method-btn:hover, .input-method-btn.active {
        border-color: #063973;
        background: rgba(6, 57, 115, 0.05);
    }
    .input-method-btn.active {
        box-shadow: 0 0 0 3px rgba(6, 57, 115, 0.1);
    }
    .search-type-btn {
        border: 2px solid #e9ecef;
        background: #fff;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .search-type-btn:hover {
        border-color: #063973;
    }
    .search-type-btn.active {
        border-color: #063973;
        background: rgba(6, 57, 115, 0.1);
    }
    .search-type-btn input {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Welcome Card -->
        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: linear-gradient(135deg, #063973 0%, #0a5299 100%);">
                        <i class="fas fa-user-md fa-2x text-white"></i>
                    </div>
                </div>
                <h4 class="mb-1">مرحباً {{ auth()->user()->name }}</h4>
                <p class="text-muted mb-0">أهلاً بك في لوحة تحكم الأخصائي</p>
            </div>
        </div>

        <!-- Barcode Scanner Card -->
        <div class="card mb-4 border-0 shadow">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #063973 0%, #0a5299 100%);">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-barcode me-2"></i>
                    مسح باركود الطالب
                </h5>
            </div>
            <div class="card-body p-4">
                <!-- اختيار نوع البحث -->
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">
                        <i class="fas fa-search me-1"></i>
                        البحث في:
                    </label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="search-type-btn d-block text-center p-3 rounded active" data-type="sessions">
                                <input type="radio" name="searchType" value="sessions" checked>
                                <i class="fas fa-calendar-check fa-2x mb-2 d-block text-success"></i>
                                <span class="fw-semibold">الجلسات</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="search-type-btn d-block text-center p-3 rounded" data-type="daycare">
                                <input type="radio" name="searchType" value="daycare">
                                <i class="fas fa-sun fa-2x mb-2 d-block text-warning"></i>
                                <span class="fw-semibold">الرعاية النهارية</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Input Method Selection -->
                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <button type="button" class="btn input-method-btn w-100 py-3 active" id="btnManualInput">
                            <i class="fas fa-keyboard fa-2x mb-2 d-block" style="color: #063973;"></i>
                            <span class="fw-semibold">إدخال يدوي</span>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn input-method-btn w-100 py-3" id="btnCameraInput">
                            <i class="fas fa-camera fa-2x mb-2 d-block" style="color: #063973;"></i>
                            <span class="fw-semibold">فتح الكاميرا</span>
                        </button>
                    </div>
                </div>

                <!-- Manual Input Section -->
                <div id="manualInputSection">
                    <div class="text-center mb-3">
                        <p class="text-muted mb-0">امسح باركود الطالب أو أدخل الكود يدوياً</p>
                    </div>

                    <form id="barcodeForm">
                        <div class="input-group input-group-lg mb-3">
                            <span class="input-group-text" style="background: #063973; border-color: #063973;">
                                <i class="fas fa-barcode text-white"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   id="barcodeInput"
                                   class="form-control form-control-lg text-center"
                                   placeholder="امسح الباركود هنا..."
                                   autofocus
                                   autocomplete="off">
                            <button type="submit" class="btn text-white px-4" style="background: #063973;">
                                <i class="fas fa-search me-1"></i> بحث
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Camera Section -->
                <div id="cameraSection" style="display: none;">
                    <div class="text-center mb-3">
                        <p class="text-muted mb-0">وجّه الكاميرا نحو باركود الطالب</p>
                    </div>

                    <div id="cameraContainer" class="mb-3">
                        <video id="cameraVideo" playsinline></video>
                        <div id="cameraOverlay"></div>
                        <div id="cameraScanLine"></div>
                    </div>

                    <div class="text-center">
                        <button type="button" class="btn btn-outline-danger" id="btnStopCamera">
                            <i class="fas fa-times me-1"></i> إيقاف الكاميرا
                        </button>
                    </div>

                    <!-- Camera Error -->
                    <div id="cameraError" class="alert alert-danger mt-3" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="cameraErrorText"></span>
                    </div>
                </div>

                <!-- Scanned Result -->
                <div id="scannedResult" class="alert alert-success mt-3" style="display: none;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-check-circle me-2"></i>
                            <span>تم مسح الكود: </span>
                            <strong id="scannedCode"></strong>
                        </div>
                        <span class="spinner-border spinner-border-sm"></span>
                    </div>
                </div>

                <div class="alert alert-light border mb-0 mt-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <small class="text-muted" id="searchHint">
                            عند مسح الباركود سيتم نقلك تلقائياً لجلسات الطالب
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-3">
            <div class="col-6">
                <a href="{{ route('specialist.sessions.index') }}" class="card h-100 border-0 shadow-sm text-decoration-none">
                    <div class="card-body text-center py-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: #e8f5e9;">
                            <i class="fas fa-calendar-day fa-xl text-success"></i>
                        </div>
                        <h6 class="mb-1 text-dark">الجلسات</h6>
                        <small class="text-muted">جلسات اليوم</small>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('specialist.daycare.index') }}" class="card h-100 border-0 shadow-sm text-decoration-none">
                    <div class="card-body text-center py-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: #fff3e0;">
                            <i class="fas fa-sun fa-xl text-warning"></i>
                        </div>
                        <h6 class="mb-1 text-dark">الرعاية</h6>
                        <small class="text-muted">الرعاية النهارية</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/@zxing/library@0.19.1/umd/index.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcodeInput');
    const barcodeForm = document.getElementById('barcodeForm');
    const btnManualInput = document.getElementById('btnManualInput');
    const btnCameraInput = document.getElementById('btnCameraInput');
    const btnStopCamera = document.getElementById('btnStopCamera');
    const manualInputSection = document.getElementById('manualInputSection');
    const cameraSection = document.getElementById('cameraSection');
    const cameraVideo = document.getElementById('cameraVideo');
    const cameraError = document.getElementById('cameraError');
    const cameraErrorText = document.getElementById('cameraErrorText');
    const scannedResult = document.getElementById('scannedResult');
    const scannedCode = document.getElementById('scannedCode');
    const searchHint = document.getElementById('searchHint');
    const searchTypeButtons = document.querySelectorAll('.search-type-btn');

    let codeReader = null;
    let selectedDeviceId = null;

    // روابط البحث حسب النوع
    const searchUrls = {
        sessions: '{{ route("specialist.sessions.index") }}',
        daycare: '{{ route("specialist.daycare.index") }}'
    };

    // نصوص التلميحات
    const hints = {
        sessions: 'عند مسح الباركود سيتم نقلك تلقائياً لجلسات الطالب',
        daycare: 'عند مسح الباركود سيتم نقلك تلقائياً للرعاية النهارية'
    };

    // الحصول على نوع البحث المحدد
    function getSelectedSearchType() {
        const selected = document.querySelector('input[name="searchType"]:checked');
        return selected ? selected.value : 'sessions';
    }

    // تحديث التلميح عند تغيير نوع البحث
    searchTypeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            searchTypeButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            this.querySelector('input').checked = true;
            searchHint.textContent = hints[this.dataset.type];
        });
    });

    // التركيز على حقل الباركود عند تحميل الصفحة
    barcodeInput.focus();

    // معالجة إرسال النموذج
    barcodeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (barcodeInput.value.trim() !== '') {
            const searchType = getSelectedSearchType();
            const url = searchUrls[searchType];
            window.location.href = `${url}?search=${encodeURIComponent(barcodeInput.value.trim())}`;
        }
    });

    // إعادة التركيز على الحقل
    document.addEventListener('click', function(e) {
        if (!e.target.closest('a') && !e.target.closest('button') && !e.target.closest('label') && manualInputSection.style.display !== 'none') {
            setTimeout(() => barcodeInput.focus(), 100);
        }
    });

    document.addEventListener('keypress', function(e) {
        if (document.activeElement !== barcodeInput &&
            document.activeElement.tagName !== 'INPUT' &&
            document.activeElement.tagName !== 'TEXTAREA' &&
            manualInputSection.style.display !== 'none') {
            barcodeInput.focus();
        }
    });

    // التبديل للإدخال اليدوي
    btnManualInput.addEventListener('click', function() {
        btnManualInput.classList.add('active');
        btnCameraInput.classList.remove('active');
        manualInputSection.style.display = 'block';
        cameraSection.style.display = 'none';
        stopCamera();
        barcodeInput.focus();
    });

    // التبديل للكاميرا
    btnCameraInput.addEventListener('click', function() {
        btnCameraInput.classList.add('active');
        btnManualInput.classList.remove('active');
        manualInputSection.style.display = 'none';
        cameraSection.style.display = 'block';
        cameraError.style.display = 'none';
        startCamera();
    });

    // إيقاف الكاميرا
    btnStopCamera.addEventListener('click', function() {
        btnManualInput.click();
    });

    // بدء الكاميرا
    async function startCamera() {
        try {
            codeReader = new ZXing.BrowserMultiFormatReader();

            // الحصول على قائمة الكاميرات
            const videoInputDevices = await codeReader.listVideoInputDevices();

            if (videoInputDevices.length === 0) {
                showCameraError('لم يتم العثور على كاميرا');
                return;
            }

            // اختيار الكاميرا الخلفية إن وجدت
            selectedDeviceId = videoInputDevices[0].deviceId;
            for (const device of videoInputDevices) {
                if (device.label.toLowerCase().includes('back') ||
                    device.label.toLowerCase().includes('rear') ||
                    device.label.toLowerCase().includes('environment')) {
                    selectedDeviceId = device.deviceId;
                    break;
                }
            }

            // بدء المسح
            codeReader.decodeFromVideoDevice(selectedDeviceId, 'cameraVideo', (result, err) => {
                if (result) {
                    handleScannedCode(result.text);
                }
                if (err && !(err instanceof ZXing.NotFoundException)) {
                    console.error(err);
                }
            });

        } catch (error) {
            console.error('Camera error:', error);
            if (error.name === 'NotAllowedError') {
                showCameraError('تم رفض الوصول للكاميرا. يرجى السماح بالوصول من إعدادات المتصفح');
            } else if (error.name === 'NotFoundError') {
                showCameraError('لم يتم العثور على كاميرا');
            } else {
                showCameraError('حدث خطأ في فتح الكاميرا: ' + error.message);
            }
        }
    }

    // إيقاف الكاميرا
    function stopCamera() {
        if (codeReader) {
            codeReader.reset();
            codeReader = null;
        }
    }

    // عرض خطأ الكاميرا
    function showCameraError(message) {
        cameraErrorText.textContent = message;
        cameraError.style.display = 'block';
    }

    // معالجة الكود الممسوح
    function handleScannedCode(code) {
        stopCamera();
        scannedCode.textContent = code;
        scannedResult.style.display = 'block';

        const searchType = getSelectedSearchType();
        const url = searchUrls[searchType];

        // الانتقال للصفحة المحددة
        setTimeout(() => {
            window.location.href = `${url}?search=${encodeURIComponent(code)}`;
        }, 500);
    }

    // إيقاف الكاميرا عند مغادرة الصفحة
    window.addEventListener('beforeunload', stopCamera);
});
</script>
@endpush
