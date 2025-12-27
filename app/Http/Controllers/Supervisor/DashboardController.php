<?php

namespace App\Http\Controllers\Supervisor;

use App\Models\User;
use App\Models\EmployeeAttendance;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return redirect()->route('supervisor.attendance.index');
    }
}
