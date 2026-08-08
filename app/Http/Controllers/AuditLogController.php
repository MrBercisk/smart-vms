<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('user:id,name')  // eager load kolom speisifik
        ->when($request->module,
            fn($q) => $q->where('module', $request->module))
        ->when($request->activity,
            fn($q) => $q->where('activity', $request->activity))
        ->when($request->date,
            fn($q) => $q->whereDate('created_at', $request->date))
        ->latest()
        ->paginate(20);

        return response()->json($logs);
    }
}