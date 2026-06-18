<?php

namespace App\Http\Controllers;

use App\Exports\VisitorReportExport;
use App\Models\Visit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $request->validate([
            'date'          => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id'   => 'nullable|exists:employees,id',
        ]);

        $visits = Visit::with(['visitor','employee','department'])
            ->whereDate('arrival_time', $request->date)
            ->when($request->department_id,
                fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->employee_id,
                fn($q) => $q->where('employee_id', $request->employee_id))
            ->orderBy('arrival_time')
            ->get();

        return response()->json([
            'date'         => $request->date,
            'total'        => $visits->count(),
            'visits'       => $visits,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id'   => 'nullable|exists:employees,id',
        ]);

        $filename = 'laporan-kunjungan-'
            . $request->start_date . '-sd-' . $request->end_date . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new VisitorReportExport(
                $request->start_date,
                $request->end_date,
                $request->department_id,
                $request->employee_id,
            ),
            $filename
        );
    }
    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id'   => 'nullable|exists:employees,id',
        ]);

        $visits = Visit::with(['visitor','employee','department'])
            ->whereBetween('arrival_time', [
                $request->start_date,
                $request->end_date . ' 23:59:59'
            ])
            ->when($request->department_id,
                fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->employee_id,
                fn($q) => $q->where('employee_id', $request->employee_id))
            ->orderBy('arrival_time')
            ->get();

        $pdf = Pdf::loadView('pdf.visitor-report', [
            'visits'     => $visits,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kunjungan.pdf');
    }
}
