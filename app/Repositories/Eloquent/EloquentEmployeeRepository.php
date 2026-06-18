<?php
// app/Repositories/Eloquent/EloquentEmployeeRepository.php
namespace App\Repositories\Eloquent;

use App\Models\Employee;
use App\Repositories\Interfaces\EmployeeRepositoryInterface;

class EloquentEmployeeRepository implements EmployeeRepositoryInterface
{
    public function all() { 
        return Employee::with('department')->get(); 
    }
    public function find(int $id) { 
        return Employee::with('department')->findOrFail($id); 
    }
    public function create(array $data) { 
        return Employee::create($data); 
    }
    public function update(int $id, array $data) {
        $employee = Employee::findOrFail($id);
        $employee->update($data);
        return $employee;
    }
    public function delete(int $id) { 
        return Employee::findOrFail($id)->delete(); 
    }
}