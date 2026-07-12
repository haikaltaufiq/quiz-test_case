<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class LogActivityController extends Controller
{
    public function index()
    {
        // Ambil data activity log, relasikan dengan user jika ada, urutkan dari yang terbaru, lalu pakai pagination
        $activityLogs = ActivityLog::with('user')
            ->latest()
            ->paginate(15);

        // Kirim variabel $activityLogs ke view admin.log-activity.index
        return view('admin.log-activity.index', compact('activityLogs'));
    }
}
