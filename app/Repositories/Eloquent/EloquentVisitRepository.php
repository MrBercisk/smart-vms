<?php
namespace App\Repositories\Eloquent;

use App\Models\Visit;
use App\Models\Visitor;
use App\Repositories\Interfaces\VisitorRepositoryInterface;
use App\Repositories\Interfaces\VisitRepositoryInterface;

class EloquentVisitRepository implements VisitRepositoryInterface
{
    public function all() {
        return Visit::with(['visitor','employee','department'])->latest()->get();
    }
    public function find(int $id) {
        return Visit::with(['visitor','employee','department'])->findOrFail($id);
    }
    public function findByVisitNumber(string $visitNumber) {
        return Visit::with(['visitor','employee','department'])
            ->where('visit_number', $visitNumber)
            ->firstOrFail();
    }
    public function create(array $data) {
        return Visit::create($data);
    }
    public function update(int $id, array $data) {
        $visit = Visit::findOrFail($id);
        $visit->update($data);
        return $visit;
    }
}