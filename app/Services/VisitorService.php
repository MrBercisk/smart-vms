<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Repositories\Interfaces\VisitorRepositoryInterface;

class VisitorService
{
    public function __construct(
        protected VisitorRepositoryInterface $repo
    ) {}

    public function getAll() { return $this->repo->all(); }
    public function find(int $id) { return $this->repo->find($id); }

    public function store(array $data): \App\Models\Visitor
    {
        $data['visitor_code'] = $this->generateVisitorCode();

        if (isset($data['photo'])) {
            $data['photo'] = $data['photo']->store('visitors/photos', 'public');
        }

        return $this->repo->create($data);
    }

    public function update(int $id, array $data): \App\Models\Visitor
    {
        $visitor = $this->repo->find($id);

        if (isset($data['photo'])) {
            // Hapus foto lama
            if ($visitor->photo) {
                Storage::disk('public')->delete($visitor->photo);
            }
            $data['photo'] = $data['photo']->store('visitors/photos', 'public');
        }

        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $visitor = $this->repo->find($id);
        if ($visitor->photo) {
            Storage::disk('public')->delete($visitor->photo);
        }
        return $this->repo->delete($id);
    }

    private function generateVisitorCode(): string
    {
        $last = \App\Models\Visitor::latest()->first();
        $seq = $last ? (int)substr($last->visitor_code, 3) + 1 : 1;
        return 'VST' . str_pad($seq, 4, '0', STR_PAD_LEFT);
        // Contoh: VST0001, VST0002
    }
}