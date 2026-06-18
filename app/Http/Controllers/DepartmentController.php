<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Services\DepartmentService;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(protected DepartmentService $service) {}

    public function index() {
        return response()->json($this->service->getAll());
    }
    public function store(StoreDepartmentRequest $request) {
        return response()->json($this->service->store($request->validated()), 201);
    }
    public function show(int $id) {
        return response()->json($this->service->find($id));
    }
    public function update(UpdateDepartmentRequest $request, int $id) {
        return response()->json($this->service->update($id, $request->validated()));
    }
    public function destroy(int $id) {
        $this->service->delete($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
