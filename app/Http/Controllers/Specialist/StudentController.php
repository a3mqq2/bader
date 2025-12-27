<?php

namespace App\Http\Controllers\Specialist;

use App\Models\Student;
use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    /**
     * عرض ملف الطالب للأخصائي
     * يعرض البيانات الأساسية ودراسة الحالة فقط بدون التفاصيل المالية
     */
    public function show(Student $student)
    {
        // تحميل العلاقات المطلوبة فقط (بدون الفواتير)
        $student->load(['currentCase.creator', 'sessionPackages.therapySession']);

        // جلب جلسات الطالب المسندة للأخصائي الحالي
        $sessions = $student->sessions()
            ->with(['package.therapySession'])
            ->where('specialist_id', auth()->id())
            ->orderBy('session_date', 'desc')
            ->orderBy('session_time', 'asc')
            ->limit(10)
            ->get();

        return view('specialist.students.show', compact('student', 'sessions'));
    }
}
