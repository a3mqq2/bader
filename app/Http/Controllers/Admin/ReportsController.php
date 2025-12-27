<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentCase;
use App\Models\StudentSession;
use App\Models\SessionPackage;
use App\Models\DaycareSubscription;
use App\Models\DaycareAttendance;
use App\Models\DaycareType;
use App\Models\TherapySession;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Assessment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /**
     * صفحة التقارير الرئيسية
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * تقرير الطلاب
     */
    public function students(Request $request)
    {
        $query = Student::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $students = $query->latest()->get();

        $stats = [
            'total' => $students->count(),
            'new' => $students->where('status', 'new')->count(),
            'under_assessment' => $students->where('status', 'under_assessment')->count(),
            'active' => $students->where('status', 'active')->count(),
        ];

        return view('admin.reports.print.students', compact('students', 'stats', 'request'));
    }

    /**
     * تقرير دراسات الحالة
     */
    public function cases(Request $request)
    {
        $query = StudentCase::with(['student', 'creator', 'invoice.items', 'invoice.payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $cases = $query->latest()->get();

        $stats = [
            'total' => $cases->count(),
            'completed' => $cases->where('status', 'completed')->count(),
            'in_progress' => $cases->where('status', 'in_progress')->count(),
            'pending' => $cases->where('status', 'pending')->count(),
        ];

        return view('admin.reports.print.cases', compact('cases', 'stats', 'request'));
    }

    /**
     * تقرير الجلسات
     */
    public function sessions(Request $request)
    {
        $query = StudentSession::with(['package.student', 'package.therapySession', 'package.specialist']);

        if ($request->filled('therapy_session_id')) {
            $query->whereHas('package', function ($q) use ($request) {
                $q->where('therapy_session_id', $request->therapy_session_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('session_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('session_date', '<=', $request->date_to);
        }

        $sessions = $query->orderBy('session_date', 'desc')->get();

        $total = $sessions->count();
        $stats = [
            'total' => $total,
            'scheduled' => $sessions->where('status', 'scheduled')->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
            'cancelled' => $sessions->where('status', 'cancelled')->count(),
            'absent' => $sessions->where('status', 'absent')->count(),
            'completion_rate' => $total > 0 ? ($sessions->where('status', 'completed')->count() / $total) * 100 : 0,
        ];

        return view('admin.reports.print.sessions', compact('sessions', 'stats', 'request'));
    }

    /**
     * تقرير الرعاية النهارية
     */
    public function daycare(Request $request)
    {
        $query = DaycareSubscription::with(['student', 'daycareType', 'attendances', 'invoice']);

        if ($request->filled('daycare_type_id')) {
            $query->where('daycare_type_id', $request->daycare_type_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $subscriptions = $query->latest()->get();

        $totalPresent = 0;
        $totalAbsent = 0;
        foreach ($subscriptions as $sub) {
            $totalPresent += $sub->attendances->where('status', 'present')->count();
            $totalAbsent += $sub->attendances->where('status', 'absent')->count();
        }
        $totalDays = $totalPresent + $totalAbsent;

        $stats = [
            'total_subscriptions' => $subscriptions->count(),
            'active' => $subscriptions->where('status', 'active')->count(),
            'attendance_rate' => $totalDays > 0 ? ($totalPresent / $totalDays) * 100 : 0,
            'total_present' => $totalPresent,
            'total_absent' => $totalAbsent,
        ];

        return view('admin.reports.print.daycare', compact('subscriptions', 'stats', 'request'));
    }

    /**
     * تقرير الفواتير
     */
    public function invoices(Request $request)
    {
        $query = Invoice::with(['student', 'invoiceType', 'payments']);

        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->where('status', 'paid');
            } elseif ($request->status === 'partial') {
                $query->where('status', 'partial');
            } elseif ($request->status === 'unpaid') {
                $query->where('status', 'pending');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $invoices = $query->latest()->get();

        $totalAmount = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');

        $stats = [
            'total_invoices' => $invoices->count(),
            'total_amount' => $totalAmount,
            'paid_amount' => $totalPaid,
            'remaining_amount' => $totalAmount - $totalPaid,
            'total_paid' => $totalPaid,
            'total_remaining' => $totalAmount - $totalPaid,
            'collection_rate' => $totalAmount > 0 ? ($totalPaid / $totalAmount) * 100 : 0,
            'paid_count' => $invoices->where('status', 'paid')->count(),
            'partial_count' => $invoices->where('status', 'partial')->count(),
            'unpaid_count' => $invoices->where('status', 'pending')->count(),
        ];

        return view('admin.reports.print.invoices', compact('invoices', 'stats', 'request'));
    }

    /**
     * تقرير أداء الأخصائيين
     */
    public function specialists(Request $request)
    {
        $query = User::role('specialist');

        if ($request->filled('specialist_id')) {
            $query->where('id', $request->specialist_id);
        }

        $specialists = $query->get()->map(function ($specialist) use ($request) {
            $sessionsQuery = StudentSession::whereHas('package', function ($q) use ($specialist) {
                $q->where('specialist_id', $specialist->id);
            });

            if ($request->filled('date_from')) {
                $sessionsQuery->whereDate('session_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $sessionsQuery->whereDate('session_date', '<=', $request->date_to);
            }

            $specialist->students_count = SessionPackage::where('specialist_id', $specialist->id)
                ->distinct('student_id')->count('student_id');
            $specialist->total_sessions = (clone $sessionsQuery)->count();
            $specialist->completed_sessions = (clone $sessionsQuery)->where('status', 'completed')->count();
            $specialist->scheduled_sessions = (clone $sessionsQuery)->where('status', 'scheduled')->count();
            $specialist->absent_sessions = (clone $sessionsQuery)->where('status', 'absent')->count();

            return $specialist;
        });

        $specialists = $specialists->sortByDesc('completed_sessions')->values();

        // أفضل الأخصائيين أداءً
        $topPerformers = $specialists->filter(fn($s) => $s->total_sessions > 0)
            ->sortByDesc(fn($s) => $s->total_sessions > 0 ? ($s->completed_sessions / $s->total_sessions) : 0)
            ->take(5);

        $totalSessions = $specialists->sum('total_sessions');
        $stats = [
            'total_specialists' => $specialists->count(),
            'total_sessions' => $totalSessions,
            'completed_sessions' => $specialists->sum('completed_sessions'),
            'avg_completion_rate' => $totalSessions > 0
                ? ($specialists->sum('completed_sessions') / $totalSessions) * 100
                : 0,
        ];

        return view('admin.reports.print.specialists', compact('specialists', 'stats', 'topPerformers', 'request'));
    }

    /**
     * تقرير مؤشرات الخطر
     */
    public function riskIndicators(Request $request)
    {
        $riskType = $request->get('risk_type');

        $activeStudents = Student::where('status', 'active')->with(['cases', 'currentCase'])->get();
        $allStudents = Student::with(['cases', 'currentCase'])->get();

        // حساب جميع الإحصائيات
        $atRiskCount = $activeStudents->filter(fn($s) => $s->is_at_risk ?? false)->count();
        $withoutCaseCount = $allStudents->filter(fn($s) => $s->cases->isEmpty())->count();
        $underAssessmentCount = Student::where('status', 'under_assessment')->count();

        $absent3DaysCount = $activeStudents->filter(function ($s) {
            $days = $s->days_since_last_attendance ?? null;
            return $days !== null && $days >= 3;
        })->count();

        $absentWeekCount = $activeStudents->filter(function ($s) {
            $days = $s->days_since_last_attendance ?? null;
            return $days !== null && $days >= 7;
        })->count();

        // جمع الطلاب المناسبين حسب نوع الفلتر
        $students = collect();

        if (!$riskType) {
            // جميع المؤشرات - جمع جميع الطلاب المعرضين للخطر
            $students = $allStudents->filter(function ($s) {
                $isAtRisk = $s->is_at_risk ?? false;
                $hasNoCase = $s->cases->isEmpty();
                $isUnderAssessment = $s->status === 'under_assessment';
                $days = $s->days_since_last_attendance ?? null;
                $absentLong = $days !== null && $days >= 3;

                return $isAtRisk || $hasNoCase || $isUnderAssessment || $absentLong;
            })->unique('id');
        } elseif ($riskType === 'at_risk') {
            $students = $activeStudents->filter(fn($s) => $s->is_at_risk ?? false);
        } elseif ($riskType === 'without_case') {
            $students = $allStudents->filter(fn($s) => $s->cases->isEmpty());
        } elseif ($riskType === 'under_assessment') {
            $students = Student::where('status', 'under_assessment')->with(['cases', 'currentCase'])->get();
        } elseif ($riskType === 'absent_3_days') {
            $students = $activeStudents->filter(function ($s) {
                $days = $s->days_since_last_attendance ?? null;
                return $days !== null && $days >= 3;
            });
        } elseif ($riskType === 'absent_week') {
            $students = $activeStudents->filter(function ($s) {
                $days = $s->days_since_last_attendance ?? null;
                return $days !== null && $days >= 7;
            });
        }

        $stats = [
            'total_at_risk' => $atRiskCount,
            'without_case' => $withoutCaseCount,
            'under_assessment' => $underAssessmentCount,
            'absent_3_days' => $absent3DaysCount,
            'absent_week' => $absentWeekCount,
        ];

        return view('admin.reports.print.risk', compact('students', 'stats', 'request'));
    }
}
