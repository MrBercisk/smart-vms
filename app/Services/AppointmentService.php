<?php
namespace App\Services;

use App\Models\Appointment;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;

class AppointmentService
{
    public function __construct(
        protected AppointmentRepositoryInterface $repo
    ) {}

    public function getAll() { return $this->repo->all(); }
    public function find(int $id) { return $this->repo->find($id); }

    public function store(array $data): Appointment
    {
        $data['appointment_number'] = $this->generateNumber();
        $data['status'] = 'pending';
        $appointment  =  $this->repo->create($data);

        AuditLogService::log('create_appointment', 'Appointment', $appointment->toArray());

        return $appointment;
    }

    public function approve(int $id): Appointment
    {
        $old = $this->repo->find($id)->toArray();
        $appointment = $this->repo->update($id, ['status' => 'approved']);

        AuditLogService::log('approve_appointment', 'Appointment', $appointment->toArray(), $old);

        return $appointment;
    }

    public function reject(int $id, string $reason): Appointment
    {
        $old = $this->repo->find($id)->toArray();
        $appointment = $this->repo->update($id, [
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);

        AuditLogService::log('reject_appointment', 'Appointment', $appointment->toArray(), $old);

        return $appointment;
    }

    public function update(int $id, array $data): Appointment
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function generateNumber(): string
    {
        $prefix = 'APT';
        $date = now()->format('Ymd');
        $last = Appointment::whereDate('created_at', today())->latest()->first();
        $seq = $last ? (int)substr($last->appointment_number, -4) + 1 : 1;
        return $prefix . $date . str_pad($seq, 4, '0', STR_PAD_LEFT);
        // Contoh: APT202506170001
    }
}