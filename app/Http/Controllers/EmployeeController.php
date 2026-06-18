<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Services\EmployeeService;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeService $service) {}

    public function index() {
        return response()->json($this->service->getAll());
    }
    public function store(StoreEmployeeRequest $request) {
        return response()->json($this->service->store($request->validated()), 201);
    }
    public function show(int $id) {
        return response()->json($this->service->find($id));
    }
    public function update(UpdateEmployeeRequest $request, int $id) {
        return response()->json($this->service->update($id, $request->validated()));
    }
    public function destroy(int $id) {
        $this->service->delete($id);
        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
