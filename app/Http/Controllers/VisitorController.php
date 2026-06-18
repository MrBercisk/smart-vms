<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreVisitorRequest;
use App\Http\Requests\UpdateVisitorRequest;
use App\Services\VisitorService;

class VisitorController extends Controller
{
     public function __construct(protected VisitorService $service) {}

    public function index() {
        return response()->json($this->service->getAll());
    }

    public function store(StoreVisitorRequest $request) {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
        }
        return response()->json($this->service->store($data), 201);
    }

    public function show(int $id) {
        return response()->json($this->service->find($id));
    }

    public function update(UpdateVisitorRequest $request, int $id) {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
        }
        return response()->json($this->service->update($id, $data));
    }

    public function destroy(int $id) {
        $this->service->delete($id);
        return response()->json(['message' => 'Visitor deleted successfully']);
    }
}
