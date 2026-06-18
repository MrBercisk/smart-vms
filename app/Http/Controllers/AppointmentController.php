<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Mail\AppointmentApprovedMail;
use App\Mail\AppointmentRejectedMail;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function __construct(protected AppointmentService $service) {}

    public function index() {
        return response()->json($this->service->getAll());
    }

    public function store(StoreAppointmentRequest $request) {
        $appointment = $this->service->store($request->validated());
        return response()->json($appointment, 201);
    }

    public function show(int $id) {
        return response()->json($this->service->find($id));
    }

    public function approve(int $id) {
        $appointment = $this->service->approve($id);

        // Kirim email ke visitor
        if ($appointment->visitor->email) {
            Mail::to($appointment->visitor->email)
                ->queue(new AppointmentApprovedMail($appointment));
        }

        return response()->json($appointment);
    }

    public function reject(RejectAppointmentRequest $request, int $id) {
        $appointment = $this->service->reject($id, $request->rejection_reason);

        if ($appointment->visitor->email) {
            Mail::to($appointment->visitor->email)
                ->queue(new AppointmentRejectedMail($appointment));
        }

        return response()->json($appointment);
    }

    public function destroy(int $id) {
        $this->service->delete($id);
        return response()->json(['message' => 'Appointment deleted successfully']);
    }
}
