<?php
namespace App\Services;

use App\Models\Visit;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Interfaces\VisitorRepositoryInterface;
use App\Repositories\Interfaces\VisitRepositoryInterface;
use Illuminate\Support\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VisitService
{
    public function __construct(
        protected VisitRepositoryInterface $repo
    ) {}

    public function getAll() { return $this->repo->all(); }
    public function find(int $id) { return $this->repo->find($id); }

    public function checkIn(array $data): Visit
    {
        $visitNumber = $this->generateVisitNumber();

        // Handle upload foto
        if (isset($data['visitor_photo'])) {
            $data['visitor_photo'] = $data['visitor_photo']
                ->store('visits/photos', 'public');
        }
        if (isset($data['identity_photo'])) {
            $data['identity_photo'] = $data['identity_photo']
                ->store('visits/identity', 'public');
        }

        $visit = $this->repo->create([
            ...$data,
            'visit_number' => $visitNumber,
            'arrival_time' => now(),
            'status'       => 'checked_in',
        ]);

        // Generate QR Code
        $qr = QrCode::format('svg')->size(200)->generate($visitNumber);
        $qrPath = 'visits/qrcodes/' . $visitNumber . '.svg';
        Storage::disk('public')->put($qrPath, $qr);

        $visit->update(['qr_code' => $qrPath]);

        return $visit->fresh(['visitor','employee','department']);
    }

    public function checkOut(string $visitNumber): Visit
    {
        $visit = $this->repo->findByVisitNumber($visitNumber);

        if ($visit->status !== 'checked_in') {
            throw new \Exception('Visitor belum check in atau sudah check out.');
        }

        $arrival  = Carbon::parse($visit->arrival_time);
        $exit     = now();
        $duration = $arrival->diffInMinutes($exit);

        $this->repo->update($visit->id, [
            'exit_time'        => $exit,
            'duration_minutes' => $duration,
            'status'           => 'checked_out',
        ]);

        return $visit->fresh(['visitor','employee','department']);
    }

    private function generateVisitNumber(): string
    {
        $prefix = 'VST';
        $date   = now()->format('Ymd');
        $last   = Visit::whereDate('created_at', today())->latest()->first();
        $seq    = $last ? (int)substr($last->visit_number, -4) + 1 : 1;
        return $prefix . $date . str_pad($seq, 4, '0', STR_PAD_LEFT);
        // Contoh: VST202506170001
    }
}