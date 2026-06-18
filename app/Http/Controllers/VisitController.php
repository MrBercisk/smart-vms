<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckInRequest;
use App\Mail\VisitorArrivedMail;
use App\Services\VisitService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class VisitController extends Controller
{
    public function __construct(protected VisitService $service) {}

    public function index() {
        return response()->json($this->service->getAll());
    }

    public function show(int $id) {
        return response()->json($this->service->find($id));
    }

    public function checkIn(CheckInRequest $request) {
        $data = $request->validated();

        if ($request->hasFile('visitor_photo')) {
            $data['visitor_photo'] = $request->file('visitor_photo');
        }
        if ($request->hasFile('identity_photo')) {
            $data['identity_photo'] = $request->file('identity_photo');
        }

        $visit = $this->service->checkIn($data);

        // Kirim email ke employee
        if ($visit->employee->email) {
            Mail::to($visit->employee->email)
                ->queue(new VisitorArrivedMail($visit));
        }

        return response()->json($visit, 201);
    }

    public function checkOut(Request $request) {
        $request->validate([
            'visit_number' => 'required|string|exists:visits,visit_number',
        ]);

        $visit = $this->service->checkOut($request->visit_number);
        return response()->json([
            'visit'          => $visit,
            'duration_label' => $visit->duration_label,
        ]);
    }

    public function printPass(int $id) {
        $visit = $this->service->find($id);
        $qr    = Storage::disk('public')->get($visit->qr_code);

        $pdf = Pdf::loadView('pdf.visitor-pass', [
            'visit' => $visit,
            'qr'    => $qr,
        ])->setPaper([0, 0, 400, 500]);

        return $pdf->stream('visitor-pass-' . $visit->visit_number . '.pdf');
    }
}
