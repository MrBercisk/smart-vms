<?php
namespace App\Services;

use App\Repositories\Interfaces\DepartmentRepositoryInterface;

class DepartmentService
{
    public function __construct(
        protected DepartmentRepositoryInterface $repo
    ) {}

    public function getAll() { return $this->repo->all(); }
    public function store(array $data) { return $this->repo->create($data); }
    public function update(int $id, array $data) { return $this->repo->update($id, $data); }
    public function delete(int $id) { return $this->repo->delete($id); }
}
?>