<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentSession;
use App\Models\StudentCase;
use App\Models\DaycareSubscription;
use App\Models\DaycareAttendance;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Models\Assessment;
use App\Models\EmployeeAttendance;
use App\Models\ExcusedAbsence;
use App\Models\InvoiceItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        // ========================================
        // 1️⃣ مؤشرات الطلاب
        // ========================================
        $studentStats = [
            'total' => Student::count(),
            'new_this_week' => Student::where('created_at', '>=', $weekStart)->count(),
            'under_assessment' => Student::where('status', 'under_assessment')->count(),
            'active' => Student::where('status', 'active')->count(),
            'new' => Student::where('status', 'new')->count(),
        ];

        // ========================================
        // 2️⃣ مؤشرات الجلسات
        // ========================================
        $sessionStats = [
            'today' => StudentSession::whereDate('session_date', $today)->count(),
            'this_week' => StudentSession::whereBetween('session_date', [$weekStart, $weekEnd])->count(),
            'completed_today' => StudentSession::whereDate('session_date', $today)
                ->where('status', 'completed')->count(),
            'completed_week' => StudentSession::whereBetween('session_date', [$weekStart, $weekEnd])
                ->where('status', 'completed')->count(),
            'cancelled_week' => StudentSession::whereBetween('session_date', [$weekStart, $weekEnd])
                ->whereIn('status', ['cancelled', 'postponed'])->count(),
            'absent_week' => StudentSession::whereBetween('session_date', [$weekStart, $weekEnd])
                ->where('status', 'absent')->count(),
        ];

        // ========================================
        // 3️⃣ مؤشرات الرعاية النهارية
        // ========================================
        $daycareStats = [
            'active_students' => DaycareSubscription::active()->count(),
            'present_today' => DaycareAttendance::whereDate('date', $today)
                ->where('status', 'present')->count(),
            'absent_today' => DaycareAttendance::whereDate('date', $today)
                ->where('status', 'absent')->count(),
            'pending_today' => DaycareAttendance::whereDate('date', $today)
                ->where('status', 'pending')->count(),
        ];

        // ========================================
        // 4️⃣ مؤشرات الخطر - غياب الطلاب
        // ========================================
        // طلاب غابوا أكثر من 3 أيام (بدون إذن)
        $studentsAbsentOver3Days = $this->getStudentsWithAbsenceDays(3);
        // طلاب غابوا أكثر من أسبوع (بدون إذن)
        $studentsAbsentOverWeek = $this->getStudentsWithAbsenceDays(7);

        // تفصيل الغياب
        $absenceRisk = [
            'over_3_days_unexcused' => count(array_filter($studentsAbsentOver3Days, fn($s) => !$s['is_excused'])),
            'over_3_days_excused' => count(array_filter($studentsAbsentOver3Days, fn($s) => $s['is_excused'])),
            'over_week_unexcused' => count(array_filter($studentsAbsentOverWeek, fn($s) => !$s['is_excused'])),
            'over_week_excused' => count(array_filter($studentsAbsentOverWeek, fn($s) => $s['is_excused'])),
            'students_over_week' => $studentsAbsentOverWeek,
        ];

        // ========================================
        // 5️⃣ مؤشرات الخطر - تأخر التقييم
        // ========================================
        $assessmentRisk = [
            'without_case' => Student::whereDoesntHave('cases')->count(),
            'under_assessment_over_14_days' => Student::where('status', 'under_assessment')
                ->where('updated_at', '<', Carbon::now()->subDays(14))->count(),
            'pending_cases' => StudentCase::where('status', 'pending')->count(),
            'in_progress_cases' => StudentCase::where('status', 'in_progress')->count(),
        ];

        // ========================================
        // 6️⃣ الحالة المالية
        // ========================================
        $unpaidInvoices = Invoice::where('status', 'pending')->get();
        $partialInvoices = Invoice::where('status', 'partial')->get();

        $financialStats = [
            'unpaid_count' => $unpaidInvoices->count(),
            'partial_count' => $partialInvoices->count(),
            'total_debt' => $unpaidInvoices->sum('balance') + $partialInvoices->sum('balance'),
            'unpaid_amount' => $unpaidInvoices->sum('balance'),
            'partial_amount' => $partialInvoices->sum('balance'),
            'old_invoices' => Invoice::whereIn('status', ['pending', 'partial'])
                ->where('created_at', '<', Carbon::now()->subDays(30))->count(),
        ];

        // ========================================
        // 7️⃣ التنبيهات السريعة
        // ========================================
        $alerts = $this->generateAlerts($studentsAbsentOverWeek, $daycareStats, $financialStats, $assessmentRisk);

        // ========================================
        // 8️⃣ آخر النشاطات
        // ========================================
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ========================================
        // 9️⃣ إحصائيات الموظفين
        // ========================================
        $totalEmployees = User::where('is_active', true)->count();
        $presentEmployees = EmployeeAttendance::whereDate('date', $today)
            ->whereNotNull('check_in')
            ->count();
        $absentEmployees = $totalEmployees - $presentEmployees;

        $employeeStats = [
            'total' => $totalEmployees,
            'present_today' => $presentEmployees,
            'absent_today' => $absentEmployees,
        ];

        // ========================================
        // 🔟 إحصائيات الغياب بإذن وبدون إذن
        // ========================================
        $excusedAbsenceToday = ExcusedAbsence::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->distinct('student_id')
            ->count('student_id');

        $studentAbsenceStats = [
            'excused' => $excusedAbsenceToday,
            'unexcused' => $absenceRisk['over_3_days_unexcused'],
        ];

        // ========================================
        // 1️⃣1️⃣ تقييمات اليوم
        // ========================================
        $todayAssessmentsCount = InvoiceItem::whereDate('created_at', $today)
            ->whereNotNull('assessment_id')
            ->count();

        // ========================================
        // 1️⃣2️⃣ بيانات الرسوم البيانية - آخر 7 أيام
        // ========================================
        $chartData = $this->getChartData();

        return view('admin.dashboard', compact(
            'studentStats',
            'sessionStats',
            'daycareStats',
            'absenceRisk',
            'assessmentRisk',
            'financialStats',
            'alerts',
            'recentActivities',
            'employeeStats',
            'studentAbsenceStats',
            'todayAssessmentsCount',
            'chartData'
        ));
    }

    /**
     * الحصول على بيانات الرسوم البيانية
     */
    private function getChartData(): array
    {
        $days = collect();
        $sessionsData = collect();
        $daycareData = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days->push($date->translatedFormat('D'));

            $sessionsData->push(
                StudentSession::whereDate('session_date', $date)
                    ->where('status', 'completed')
                    ->count()
            );

            $daycareData->push(
                DaycareAttendance::whereDate('date', $date)
                    ->where('status', 'present')
                    ->count()
            );
        }

        return [
            'labels' => $days->toArray(),
            'sessions' => $sessionsData->toArray(),
            'daycare' => $daycareData->toArray(),
        ];
    }

    /**
     * الحصول على الطلاب الذين لديهم غياب متتالي
     */
    private function getStudentsWithAbsenceDays(int $days): array
    {
        $students = Student::where('status', 'active')->get();
        $result = [];

        foreach ($students as $student) {
            $daysSinceAttendance = $student->days_since_last_attendance;

            if ($daysSinceAttendance !== null && $daysSinceAttendance >= $days) {
                // التحقق من وجود إذن غياب نشط
                $hasActiveExcuse = $student->activeExcusedAbsences()->exists();

                $result[] = [
                    'student' => $student,
                    'days' => $daysSinceAttendance,
                    'is_excused' => $hasActiveExcuse,
                    'last_attendance' => $student->last_attendance_date,
                ];
            }
        }

        // ترتيب حسب عدد الأيام (الأكثر أولاً)
        usort($result, fn($a, $b) => $b['days'] <=> $a['days']);

        return $result;
    }

    /**
     * توليد التنبيهات السريعة
     */
    private function generateAlerts(array $studentsAbsentOverWeek, array $daycareStats, array $financialStats, array $assessmentRisk): array
    {
        $alerts = [];

        // تنبيهات الغياب
        foreach (array_slice($studentsAbsentOverWeek, 0, 5) as $item) {
            if (!$item['is_excused']) {
                $alerts[] = [
                    'type' => 'danger',
                    'icon' => 'ti ti-alert-triangle',
                    'message' => 'الطالب ' . $item['student']->name . ' غائب منذ ' . $item['days'] . ' يوم بدون إذن',
                    'link' => route('admin.students.show', $item['student']->id),
                ];
            }
        }

        // جلسات بدون أخصائي
        $sessionsWithoutSpecialist = StudentSession::whereDate('session_date', Carbon::today())
            ->whereNull('specialist_id')
            ->count();
        if ($sessionsWithoutSpecialist > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'ti ti-user-off',
                'message' => $sessionsWithoutSpecialist . ' جلسات اليوم بدون أخصائي',
                'link' => route('admin.sessions.today'),
            ];
        }

        // طلاب رعاية بدون تسجيل حضور اليوم
        if ($daycareStats['pending_today'] > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'ti ti-clock',
                'message' => $daycareStats['pending_today'] . ' طالب رعاية نهارية بدون تسجيل حضور اليوم',
                'link' => route('admin.daycare.index'),
            ];
        }

        // فواتير قديمة غير مدفوعة
        if ($financialStats['old_invoices'] > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'ti ti-receipt-off',
                'message' => $financialStats['old_invoices'] . ' فاتورة قديمة غير مدفوعة (أكثر من 30 يوم)',
                'link' => '#',
            ];
        }

        // طلاب بدون دراسة حالة
        if ($assessmentRisk['without_case'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'ti ti-file-off',
                'message' => $assessmentRisk['without_case'] . ' طالب بدون دراسة حالة',
                'link' => route('admin.students.index'),
            ];
        }

        return $alerts;
    }

    /**
     * التقييمات
     */
    public function todayAssessments()
    {
        $query = \App\Models\InvoiceItem::with([
            'invoice.student',
            'invoice.studentCase',
            'assessment',
            'assessor'
        ])->whereHas('invoice.studentCase');

        // الفلاتر
        if (request()->has('student_id') && request('student_id')) {
            $query->whereHas('invoice', function($q) {
                $q->where('student_id', request('student_id'));
            });
        }

        if (request()->has('assessment_id') && request('assessment_id')) {
            $query->where('assessment_id', request('assessment_id'));
        }

        if (request()->has('status') && request('status')) {
            $query->where('assessment_status', request('status'));
        }

        if (request()->has('case_status') && request('case_status')) {
            $query->whereHas('invoice.studentCase', function($q) {
                $q->where('status', request('case_status'));
            });
        }

        if (request()->has('date_from') && request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }

        if (request()->has('date_to') && request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        $assessmentItems = $query->latest()->paginate(20);

        // البيانات للفلاتر
        $students = Student::orderBy('name')->get();
        $allAssessments = Assessment::where('is_active', true)->orderBy('name')->get();

        return view('admin.today-assessments', compact('assessmentItems', 'students', 'allAssessments'));
    }

    /**
     * الحصول على تفاصيل دراسة الحالة (AJAX)
     */
    public function getCaseDetails(StudentCase $case)
    {
        $case->load(['student', 'creator']);

        return response()->json([
            'success' => true,
            'case' => [
                'id' => $case->id,
                'status' => $case->status,
                'status_text' => $case->status_text,
                'status_color' => $case->status_color,
                'notes' => $case->notes,
                'created_at' => $case->created_at->format('Y/m/d h:i A'),
                'creator_name' => $case->creator->name ?? null,
            ],
            'student' => [
                'name' => $case->student->name,
                'code' => $case->student->code,
            ]
        ]);
    }

    /**
     * الحصول على ملاحظات عنصر التقييم (AJAX)
     */
    public function getAssessmentNotes(\App\Models\InvoiceItem $item)
    {
        $item->load('assessor');

        return response()->json([
            'success' => true,
            'item' => [
                'assessment_name' => $item->assessment_name,
                'assessment_result' => $item->assessment_result,
                'assessment_notes' => $item->assessment_notes,
                'assessor_name' => $item->assessor->name ?? null,
                'assessed_at' => $item->assessed_at ? $item->assessed_at->format('Y/m/d h:i A') : null,
            ]
        ]);
    }

    /**
     * طباعة قائمة التقييمات
     */
    public function printAssessments()
    {
        $query = \App\Models\InvoiceItem::with([
            'invoice.student',
            'invoice.studentCase',
            'assessment',
            'assessor'
        ])->whereHas('invoice.studentCase');

        // الفلاتر
        if (request()->has('student_id') && request('student_id')) {
            $query->whereHas('invoice', function($q) {
                $q->where('student_id', request('student_id'));
            });
        }

        if (request()->has('assessment_id') && request('assessment_id')) {
            $query->where('assessment_id', request('assessment_id'));
        }

        if (request()->has('status') && request('status')) {
            $query->where('assessment_status', request('status'));
        }

        if (request()->has('case_status') && request('case_status')) {
            $query->whereHas('invoice.studentCase', function($q) {
                $q->where('status', request('case_status'));
            });
        }

        if (request()->has('date_from') && request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }

        if (request()->has('date_to') && request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        $assessmentItems = $query->latest()->get();

        // البيانات للفلاتر
        $students = Student::all();
        $allAssessments = Assessment::where('is_active', true)->get();

        return view('admin.assessments.print-list', compact('assessmentItems', 'students', 'allAssessments'));
    }
}
