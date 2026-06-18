<?php
// app/Services/EmployeeService.php
namespace App\Services;

use App\Repositories\Interfaces\EmployeeRepositoryInterface;

class EmployeeService
{
    public function __construct(
        protected EmployeeRepositoryInterface $repo
    ) {}

    public function getAll() { return $this->repo->all(); }
    public function find(int $id) { return $this->repo->find($id); }
    public function store(array $data) {
        $data['employee_id'] = $this->generateEmployeeId();
        return $this->repo->create($data);
    }
    public function update(int $id, array $data) { 
        return $this->repo->update($id, $data); 
    }
    public function delete(int $id) { 
        return $this->repo->delete($id); 
    }

    private function generateEmployeeId(): string {
        $last = \App\Models\Employee::latest()->first();
        $seq = $last ? (int)substr($last->employee_id, 3) + 1 : 1;
        return 'EMP' . str_pad($seq, 4, '0', STR_PAD_LEFT);
        // Contoh: EMP0001, EMP0002
    }
}