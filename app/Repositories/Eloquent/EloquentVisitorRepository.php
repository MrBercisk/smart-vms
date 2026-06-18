<?php
namespace App\Repositories\Eloquent;

use App\Models\Visitor;
use App\Repositories\Interfaces\VisitorRepositoryInterface;

class EloquentVisitorRepository implements VisitorRepositoryInterface
{
    public function all() { return Visitor::all(); }
    public function find(int $id) { return Visitor::findOrFail($id); }
    public function create(array $data) { return Visitor::create($data); }
    public function update(int $id, array $data) {
        $visitor = Visitor::findOrFail($id);
        $visitor->update($data);
        return $visitor;
    }
    public function delete(int $id) { return Visitor::findOrFail($id)->delete(); }
}