<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\StudentCase;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Apply filters to query.
     */
    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%')
                    ->orWhere('guardian_name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

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

        // الترتيب الديناميكي
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        // التحقق من الأعمدة المسموح بها
        $allowedColumns = ['code', 'name', 'birth_date', 'gender', 'guardian_name', 'phone', 'status', 'created_at'];
        if (in_array($sortBy, $allowedColumns)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = Student::query();

        // فلاتر مؤشرات الخطر
        if ($request->filled('risk_filter')) {
            switch ($request->risk_filter) {
                case 'without_case':
                    $query->whereDoesntHave('cases');
                    break;
                case 'under_assessment':
                    $query->where('status', 'under_assessment');
                    break;
                case 'assessment_delayed':
                    $query->where('status', 'under_assessment')
                        ->where('updated_at', '<', Carbon::now()->subDays(14));
                    break;
                case 'at_risk':
                case 'absent_3_days':
                case 'absent_week':
                    // هذه الفلاتر تعتمد على computed attributes
                    // سيتم معالجتها في الأسفل بعد جلب البيانات (السطر 95)
                    break;
            }
        }

        $this->applyFilters($request, $query);

        // فلترة خاصة تحتاج معالجة بعد جلب البيانات
        if ($request->filled('risk_filter') && in_array($request->risk_filter, ['at_risk', 'absent_3_days', 'absent_week'])) {
            $allStudents = $query->get();

            $filteredStudents = $allStudents->filter(function ($student) use ($request) {
                $daysSince = $student->days_since_last_attendance;

                if ($daysSince === null) {
                    return false;
                }

                switch ($request->risk_filter) {
                    case 'at_risk':
                        return $student->is_at_risk;
                    case 'absent_3_days':
                        return $daysSince >= 3;
                    case 'absent_week':
                        return $daysSince >= 7;
                }

                return false;
            });

            // تحويل للـ paginator يدوي
            $page = $request->get('page', 1);
            $perPage = 12;
            $students = new \Illuminate\Pagination\LengthAwarePaginator(
                $filteredStudents->forPage($page, $perPage),
                $filteredStudents->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $students = $query->paginate(12)->withQueryString();
        }

        // إحصائيات مؤشرات الخطر للعرض
        $riskStats = $this->getRiskStats();

        return view('admin.students.index', compact('students', 'riskStats'));
    }

    /**
     * Get risk statistics for quick filters
     */
    private function getRiskStats(): array
    {
        $stats = [
            'without_case' => Student::whereDoesntHave('cases')->count(),
            'under_assessment' => Student::where('status', 'under_assessment')->count(),
            'assessment_delayed' => Student::where('status', 'under_assessment')
                ->where('updated_at', '<', Carbon::now()->subDays(14))->count(),
        ];

        // حساب الطلاب المعرضين للخطر (غياب)
        $activeStudents = Student::where('status', 'active')->get();
        $stats['at_risk'] = $activeStudents->filter(fn($s) => $s->is_at_risk)->count();
        $stats['absent_3_days'] = $activeStudents->filter(fn($s) => ($s->days_since_last_attendance ?? 0) >= 3)->count();
        $stats['absent_week'] = $activeStudents->filter(fn($s) => ($s->days_since_last_attendance ?? 0) >= 7)->count();

        return $stats;
    }

    /**
     * Print students list.
     */
    public function print(Request $request)
    {
        $query = Student::query();
        $this->applyFilters($request, $query);

        $students = $query->get();

        return view('admin.students.print-list', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'guardian_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_alt' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:new,active',
        ]);

        $student = Student::create([
            'name' => $request->name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'guardian_name' => $request->guardian_name,
            'phone' => $request->phone,
            'phone_alt' => $request->phone_alt,
            'address' => $request->address,
            'notes' => $request->notes,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        ActivityLog::log("إضافة طالب جديد: {$student->name}", $student, 'create');

        return redirect()->route('admin.students.index')
            ->with('success', 'تم إضافة الطالب بنجاح');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load([
            'currentCase.creator',
            'currentCase.invoice.items.assessor',
            'invoices.items',
            'invoices.payments.creator',
            'sessionPackages.sessions',
            'sessionPackages.therapySession',
            'sessionPackages.specialist',
            'daycareSubscriptions.attendances',
            'excusedAbsences.creator',
            'activeExcusedAbsences',
            'creator'
        ]);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'guardian_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'phone_alt' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:new,active',
        ]);

        $student->update([
            'name' => $request->name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'guardian_name' => $request->guardian_name,
            'phone' => $request->phone,
            'phone_alt' => $request->phone_alt,
            'address' => $request->address,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        ActivityLog::log("تحديث بيانات الطالب: {$student->name}", $student, 'update');

        return redirect()->route('admin.students.index')
            ->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        $studentName = $student->name;
        $student->delete();

        ActivityLog::log("حذف الطالب: {$studentName}", null, 'delete');

        return redirect()->route('admin.students.index')
            ->with('success', 'تم حذف الطالب بنجاح');
    }

    /**
     * Toggle student status.
     */
    public function toggleStatus(Student $student)
    {
        $student->update([
            'status' => $student->status === 'new' ? 'active' : 'new'
        ]);

        $message = $student->status === 'active' ? 'تم تفعيل الطالب بنجاح' : 'تم تغيير حالة الطالب إلى جديد';
        return redirect()->route('admin.students.index')->with('success', $message);
    }

    /**
     * Print student card.
     */
    public function printCard(Student $student)
    {
        return view('admin.students.print-card', compact('student'));
    }
}
