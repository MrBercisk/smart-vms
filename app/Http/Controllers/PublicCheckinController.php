<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Visitor;
use App\Mail\VisitorArrivedMail;
use App\Models\Visit;
use App\Services\VisitService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PublicCheckinController extends Controller
{
    public function __construct(protected VisitService $service) {}

    public function departments() {
        return response()->json(
            Department::select('id', 'department_name')->get()
        );
    }

    public function employees(Request $request) {
        return response()->json(
            Employee::select('id', 'employee_name', 'department_id')
                ->where('status', 'active')
                ->when($request->department_id,
                    fn($q) => $q->where('department_id', $request->department_id))
                ->get()
        );
    }

    public function checkin(Request $request)
    {
        $validated = $request->validate([
            'full_name'       => 'required|string|max:100',
            'company_name'    => 'nullable|string|max:100',
            'phone'           => 'required|string|max:20',
            'email'           => 'nullable|email',
            'identity_type'   => 'required|in:KTP,SIM,Passport',
            'identity_number' => 'required|string|max:50',
            'employee_id'     => 'required|exists:employees,id',
            'department_id'   => 'required|exists:departments,id',
            'purpose'         => 'required|string',
            'visitor_photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Cek apakah visitor sudah pernah terdaftar (by identity_number)
        $visitor = Visitor::firstOrCreate(
            ['identity_number' => $validated['identity_number']],
            [
                'visitor_code'    => $this->generateVisitorCode(),
                'full_name'       => $validated['full_name'],
                'company_name'    => $validated['company_name'] ?? null,
                'phone'           => $validated['phone'],
                'email'           => $validated['email'] ?? null,
                'identity_type'   => $validated['identity_type'],
                'status'          => 'active',
            ]
        );

        // Cek apakah visitor di-blacklist
        if ($visitor->status === 'blacklist') {
            return response()->json([
                'message' => 'Maaf, Anda tidak dapat melakukan check-in. Silakan hubungi resepsionis.'
            ], 403);
        }

        $checkinData = [
            'visitor_id'    => $visitor->id,
            'employee_id'   => $validated['employee_id'],
            'department_id' => $validated['department_id'],
            'purpose'       => $validated['purpose'],
        ];

        if ($request->hasFile('visitor_photo')) {
            $checkinData['visitor_photo'] = $request->file('visitor_photo');
        }

        $visit = $this->service->checkIn($checkinData);

        if ($visit->employee->email) {
            Mail::to($visit->employee->email)
                ->queue(new VisitorArrivedMail($visit));
        }

        return response()->json([
            'visit_id'     => $visit->id,  
            'visit_number' => $visit->visit_number,
            'visitor_name' => $visit->visitor->full_name,
            'employee'     => $visit->employee->employee_name,
            'message'      => 'Check-in berhasil! Silakan tunggu',
        ], 201);
    }

    private function generateVisitorCode(): string
    {
        $last = Visitor::latest()->first();
        $seq  = $last ? (int)substr($last->visitor_code, 3) + 1 : 1;
        return 'VST' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
    public function printPass(int $id)
    {
        $visit = Visit::with(['visitor', 'employee', 'department'])->findOrFail($id);
        $qrContent = Storage::disk('public')->get($visit->qr_code);
        $qrBase64  = 'data:image/svg+xml;base64,' . base64_encode($qrContent);

        $pdf = Pdf::loadView('pdf.visitor-pass', [
            'visit'   => $visit,
            'qrImage' => $qrBase64,
        ])->setPaper([0, 0, 400, 500]);

        return $pdf->stream('visitor-pass-' . $visit->visit_number . '.pdf');
    }

    // cekout
    public function lookup(string $visitNumber)
    {
        $visit = \App\Models\Visit::with(['visitor', 'employee', 'department'])
            ->where('visit_number', $visitNumber)
            ->first();

        if (!$visit) {
            return response()->json(['message' => 'Nomor kunjungan tidak ditemukan.'], 404);
        }

        if ($visit->status === 'checked_out') {
            return response()->json(['message' => 'Kunjungan ini sudah selesai check-out sebelumnya.'], 422);
        }

        return response()->json([
            'visit_number'  => $visit->visit_number,
            'visitor_name'  => $visit->visitor->full_name,
            'employee'      => $visit->employee->employee_name,
            'department'    => $visit->department->department_name,
            'arrival_time'  => $visit->arrival_time?->format('d/m/Y H:i'),
            'status'        => $visit->status,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'visit_number' => 'required|string|exists:visits,visit_number',
        ]);

        $visit = $this->service->checkOut($request->visit_number);

        return response()->json([
            'visit_number'   => $visit->visit_number,
            'duration_label' => $visit->duration_label,
            'message'        => 'Check-out berhasil. Terima kasih atas kunjungan Anda!',
        ]);
    }
}
