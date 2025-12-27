<?php

namespace App\Http\Controllers\Admin;

use App\Models\Assessment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Assessment::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $assessments = $query->latest()->paginate(12)->withQueryString();

        return view('admin.assessments.index', compact('assessments'));
    }

    public function create()
    {
        return view('admin.assessments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $assessment = Assessment::create([
            'name' => $request->name,
            'price' => $request->price,
            'is_active' => $request->boolean('is_active', true),
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        ActivityLog::log("إضافة اختبار جديد: {$assessment->name}", $assessment, 'create');

        return redirect()->route('admin.assessments.index')
            ->with('success', 'تم إضافة الاختبار بنجاح');
    }

    public function edit(Assessment $assessment)
    {
        return view('admin.assessments.edit', compact('assessment'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $assessment->update([
            'name' => $request->name,
            'price' => $request->price,
            'is_active' => $request->boolean('is_active', true),
            'description' => $request->description,
        ]);

        ActivityLog::log("تحديث الاختبار: {$assessment->name}", $assessment, 'update');

        return redirect()->route('admin.assessments.index')
            ->with('success', 'تم تحديث الاختبار بنجاح');
    }

    public function destroy(Assessment $assessment)
    {
        // منع حذف سجل دراسة الحالة الأساسي
        if ($assessment->id === 1) {
            return redirect()->route('admin.assessments.index')
                ->with('error', 'لا يمكن حذف دراسة الحالة الأساسية');
        }

        $assessmentName = $assessment->name;
        $assessment->delete();

        ActivityLog::log("حذف الاختبار: {$assessmentName}", null, 'delete');

        return redirect()->route('admin.assessments.index')
            ->with('success', 'تم حذف الاختبار بنجاح');
    }

    public function toggleStatus(Assessment $assessment)
    {
        $assessment->update([
            'is_active' => !$assessment->is_active
        ]);

        $status = $assessment->is_active ? 'تفعيل' : 'إلغاء تفعيل';
        ActivityLog::log("{$status} الاختبار: {$assessment->name}", $assessment, 'update');

        $message = $assessment->is_active ? 'تم تفعيل الاختبار بنجاح' : 'تم إلغاء تفعيل الاختبار بنجاح';
        return redirect()->route('admin.assessments.index')->with('success', $message);
    }
}
