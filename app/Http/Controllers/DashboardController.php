<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Visit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Summary cards
    public function summary()
    {
        return response()->json([
            'total_today'      => Visit::whereDate('arrival_time', today())->count(),
            'active_visitor'   => Visit::where('status', 'checked_in')->count(),
            'total_this_month' => Visit::whereMonth('arrival_time', now()->month)
                                       ->whereYear('arrival_time', now()->year)
                                       ->count(),
            'scheduled'        => Appointment::where('status', 'approved')
                                             ->whereDate('appointment_date', '>=', today())
                                             ->count(),
            'completed_today'  => Visit::where('status', 'checked_out')
                                       ->whereDate('arrival_time', today())
                                       ->count(),
        ]);
    }

    // Visitor trend 7 hari terakhir
    public function trend()
    {
        $data = Visit::selectRaw('DATE(arrival_time) as date, COUNT(*) as total')
            ->where('arrival_time', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    // Visitor per departemen
    public function byDepartment()
    {
        $data = Visit::selectRaw('department_id, COUNT(*) as total')
            ->with('department:id,department_name')
            ->whereMonth('arrival_time', now()->month)
            ->groupBy('department_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn($v) => [
                'department' => $v->department->department_name,
                'total'      => $v->total,
            ]);

        return response()->json($data);
    }

    // Peak hours
    public function peakHours()
    {
        $data = Visit::selectRaw('HOUR(arrival_time) as hour, COUNT(*) as total')
            ->whereNotNull('arrival_time')
            ->whereMonth('arrival_time', now()->month)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(fn($v) => [
                'hour'  => str_pad($v->hour, 2, '0', STR_PAD_LEFT) . ':00',
                'total' => $v->total,
            ]);

        return response()->json($data);
    }

    // Top 5 most visited employee
    public function topEmployees()
    {
        $data = Visit::selectRaw('employee_id, COUNT(*) as total')
            ->with('employee:id,employee_name')
            ->whereMonth('arrival_time', now()->month)
            ->groupBy('employee_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($v) => [
                'employee' => $v->employee->employee_name,
                'total'    => $v->total,
            ]);

        return response()->json($data);
    }

    // Top 5 most visited department
    public function topDepartments()
    {
        $data = Visit::selectRaw('department_id, COUNT(*) as total')
            ->with('department:id,department_name')
            ->whereMonth('arrival_time', now()->month)
            ->groupBy('department_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($v) => [
                'department' => $v->department->department_name,
                'total'      => $v->total,
            ]);

        return response()->json($data);
    }

    // KPI metrics
    public function kpi()
    {
        $visits = Visit::where('status', 'checked_out')
            ->whereMonth('arrival_time', now()->month)
            ->get();

        $totalVisits   = $visits->count();
        $avgDuration   = $totalVisits > 0
            ? round($visits->avg('duration_minutes'))
            : 0;

        $currentMonth  = Visit::whereMonth('arrival_time', now()->month)
            ->whereYear('arrival_time', now()->year)
            ->count();
        $previousMonth = Visit::whereMonth('arrival_time', now()->subMonth()->month)
            ->whereYear('arrival_time', now()->subMonth()->year)
            ->count();

        $growthRate = $previousMonth > 0
            ? round((($currentMonth - $previousMonth) / $previousMonth) * 100, 2)
            : 0;

        return response()->json([
            'avg_duration_minutes' => $avgDuration,
            'avg_duration_label'   => $this->formatDuration($avgDuration),
            'growth_rate'          => $growthRate,
            'total_this_month'     => $currentMonth,
            'total_previous_month' => $previousMonth,
        ]);
    }

    private function formatDuration(int $minutes): string
    {
        if ($minutes <= 0) return '-';
        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;
        if ($hours > 0 && $mins > 0) return "{$hours} jam {$mins} menit";
        if ($hours > 0) return "{$hours} jam";
        return "{$mins} menit";
    }
}