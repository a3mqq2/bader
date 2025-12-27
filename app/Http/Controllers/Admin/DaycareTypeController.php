<?php

namespace App\Http\Controllers\Admin;

use App\Models\DaycareType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DaycareTypeController extends Controller
{
    public function index()
    {
        $types = DaycareType::latest()->get();
        return view('admin.daycare-types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'اسم نوع الرعاية مطلوب',
            'price.required' => 'السعر مطلوب',
            'price.min' => 'السعر يجب أن يكون 0 أو أكثر',
        ]);

        $type = DaycareType::create([
            'name' => $request->name,
            'price' => $request->price,
            'is_active' => true,
        ]);

        ActivityLog::log("إضافة نوع رعاية: {$type->name}", $type, 'create');

        return redirect()->route('admin.daycare-types.index')
            ->with('success', 'تم إضافة نوع الرعاية بنجاح');
    }

    public function update(Request $request, DaycareType $daycareType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'اسم نوع الرعاية مطلوب',
            'price.required' => 'السعر مطلوب',
            'price.min' => 'السعر يجب أن يكون 0 أو أكثر',
        ]);

        $daycareType->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        ActivityLog::log("تحديث نوع الرعاية: {$daycareType->name}", $daycareType, 'update');

        return redirect()->route('admin.daycare-types.index')
            ->with('success', 'تم تحديث نوع الرعاية بنجاح');
    }

    public function toggleStatus(DaycareType $daycareType)
    {
        $daycareType->update([
            'is_active' => !$daycareType->is_active
        ]);

        $status = $daycareType->is_active ? 'تفعيل' : 'تعطيل';
        ActivityLog::log("{$status} نوع الرعاية: {$daycareType->name}", $daycareType, 'update');

        return redirect()->route('admin.daycare-types.index')
            ->with('success', "تم {$status} نوع الرعاية بنجاح");
    }

    public function destroy(DaycareType $daycareType)
    {
        $typeName = $daycareType->name;
        $daycareType->delete();

        ActivityLog::log("حذف نوع الرعاية: {$typeName}", null, 'delete');

        return redirect()->route('admin.daycare-types.index')
            ->with('success', 'تم حذف نوع الرعاية بنجاح');
    }
}
