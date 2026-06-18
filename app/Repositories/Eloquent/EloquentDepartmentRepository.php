<?php
// Eloquent/EloquentDepartmentRepository.php
namespace App\Repositories\Eloquent;

use App\Models\Department;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;

class EloquentDepartmentRepository implements DepartmentRepositoryInterface
{
    public function all() { return Department::all(); }
    public function find(int $id) { return Department::findOrFail($id); }
    public function create(array $data) { return Department::create($data); }
    public function update(int $id, array $data) {
        $dept = Department::findOrFail($id);
        $dept->update($data);
        return $dept;
    }
    public function delete(int $id) { return Department::findOrFail($id)->delete(); }
}
?>