<?php
// Eloquent/EloquentDepartmentRepository.php
namespace App\Repositories\Eloquent;

use App\Models\Appointment;
use App\Models\Department;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;

class EloquentAppointmentRepository implements AppointmentRepositoryInterface
{
    public function all() {
        return Appointment::with(['visitor','employee','department'])
            ->latest()->get();
    }
    public function find(int $id) {
        return Appointment::with(['visitor','employee','department'])
            ->findOrFail($id);
    }
    public function create(array $data) {
        return Appointment::create($data);
    }
    public function update(int $id, array $data) {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($data);
        return $appointment;
    }
    public function delete(int $id) {
        return Appointment::findOrFail($id)->delete();
    }
}
?>