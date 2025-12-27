<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // فلترة بالموظف
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة بالتاريخ من
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // فلترة بالتاريخ إلى
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // فلترة بنوع العملية
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // البحث في الوصف
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('admin.activity-logs.index', compact('logs', 'users'));
    }
}
