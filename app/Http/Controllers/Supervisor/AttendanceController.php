<?php

namespace App\Http\Controllers\Supervisor;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EmployeeAttendance;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    /**
     * صفحة تسجيل الحضور بالباركود
     */
    public function index()
    {
        $todayAttendances = EmployeeAttendance::with('user')
            ->today()
            ->latest('check_in')
            ->get();

        return view('supervisor.attendance.index', compact('todayAttendances'));
    }

    /**
     * تسجيل الحضور/الانصراف بالباركود
     */
    public function scan(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('code', $request->code)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على موظف بهذا الكود',
            ], 404);
        }

        $today = now()->toDateString();
        $attendance = EmployeeAttendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // تسجيل دخول جديد
            $attendance = EmployeeAttendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => now()->format('H:i:s'),
                'status' => 'present',
                'recorded_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'type' => 'check_in',
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => [
                    'name' => $user->name,
                    'code' => $user->code,
                    'role' => $user->role_text,
                ],
                'time' => now()->format('h:i A'),
                'attendance' => $this->formatAttendance($attendance),
            ]);
        }

        if ($attendance->check_out) {
            // تم تسجيل الخروج مسبقاً
            return response()->json([
                'success' => false,
                'message' => 'تم تسجيل الدخول والخروج لهذا الموظف اليوم',
                'user' => [
                    'name' => $user->name,
                    'code' => $user->code,
                ],
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out,
            ], 400);
        }

        // تسجيل خروج
        $attendance->update([
            'check_out' => now()->format('H:i:s'),
        ]);

        return response()->json([
            'success' => true,
            'type' => 'check_out',
            'message' => 'تم تسجيل الخروج بنجاح',
            'user' => [
                'name' => $user->name,
                'code' => $user->code,
                'role' => $user->role_text,
            ],
            'time' => now()->format('h:i A'),
            'work_hours' => $attendance->fresh()->formatted_work_hours,
            'attendance' => $this->formatAttendance($attendance->fresh()),
        ]);
    }

    /**
     * سجل الحضور مع الفلترة
     */
    public function log(Request $request)
    {
        $query = EmployeeAttendance::with(['user', 'recorder']);

        // فلترة بالتاريخ
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->today();
        }

        // فلترة بالموظف
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة بنطاق تاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->latest('date')->latest('check_in')->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        // إحصائيات اليوم
        $todayStats = [
            'total' => EmployeeAttendance::today()->count(),
            'present' => EmployeeAttendance::today()->where('status', 'present')->count(),
            'checked_out' => EmployeeAttendance::today()->whereNotNull('check_out')->count(),
            'still_in' => EmployeeAttendance::today()->whereNull('check_out')->count(),
        ];

        return view('supervisor.attendance.log', compact('attendances', 'users', 'todayStats'));
    }

    /**
     * طباعة تقرير الحضور
     */
    public function print(Request $request)
    {
        $query = EmployeeAttendance::with(['user', 'recorder']);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } elseif ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('date', '>=', $request->date_from)
                  ->whereDate('date', '<=', $request->date_to);
        } else {
            $query->today();
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = $query->latest('date')->latest('check_in')->get();

        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
        ];

        return view('supervisor.attendance.print', compact('attendances', 'stats', 'request'));
    }

    /**
     * تنسيق بيانات الحضور للـ JSON
     */
    private function formatAttendance($attendance)
    {
        return [
            'id' => $attendance->id,
            'user_name' => $attendance->user->name,
            'user_code' => $attendance->user->code,
            'user_role' => $attendance->user->role_text,
            'check_in' => $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : null,
            'check_out' => $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : null,
            'status' => $attendance->status,
            'status_text' => $attendance->status_text,
            'status_color' => $attendance->status_color,
            'work_hours' => $attendance->formatted_work_hours,
        ];
    }

    /**
     * جلب حضور اليوم (AJAX)
     */
    public function todayList()
    {
        $attendances = EmployeeAttendance::with('user')
            ->today()
            ->latest('check_in')
            ->get()
            ->map(fn($a) => $this->formatAttendance($a));

        return response()->json([
            'success' => true,
            'attendances' => $attendances,
        ]);
    }
}
