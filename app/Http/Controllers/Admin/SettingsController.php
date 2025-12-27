<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * صفحة الإعدادات
     */
    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * تحديث الإعدادات
     */
    public function update(Request $request)
    {
        $request->validate([
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'session_incentive_amount' => 'required|numeric|min:0',
            'daycare_incentive_amount' => 'required|numeric|min:0',
            'incentives_enabled' => 'nullable|boolean',
        ]);

        // تحديث الإعدادات
        Setting::set('work_start_time', $request->work_start_time);
        Setting::set('work_end_time', $request->work_end_time);
        Setting::set('session_incentive_amount', $request->session_incentive_amount);
        Setting::set('daycare_incentive_amount', $request->daycare_incentive_amount);
        Setting::set('incentives_enabled', $request->incentives_enabled ? '1' : '0');

        // مسح الكاش
        Cache::flush();

        ActivityLog::log('تحديث إعدادات النظام', null, 'update');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
