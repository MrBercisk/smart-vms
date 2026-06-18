<?php

namespace App\Exports;

use App\Models\Visit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;

class VisitorReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    public function __construct(
        protected string $startDate,
        protected string $endDate,
        protected ?int   $departmentId = null,
        protected ?int   $employeeId   = null,
    ) {}

    public function query()
    {
        return Visit::with(['visitor','employee','department'])
            ->whereBetween('arrival_time', [$this->startDate, $this->endDate])
            ->when($this->departmentId,
                fn($q) => $q->where('department_id', $this->departmentId))
            ->when($this->employeeId,
                fn($q) => $q->where('employee_id', $this->employeeId))
            ->orderBy('arrival_time');
    }

    public function headings(): array
    {
        return [
            'No. Kunjungan',
            'Nama Tamu',
            'Perusahaan',
            'Departemen',
            'Host / Karyawan',
            'Check In',
            'Check Out',
            'Durasi',
        ];
    }

    public function map($visit): array
    {
        $hours    = intdiv($visit->duration_minutes ?? 0, 60);
        $minutes  = ($visit->duration_minutes ?? 0) % 60;
        $duration = $visit->duration_minutes
            ? "{$hours} jam {$minutes} menit"
            : '-';

        return [
            $visit->visit_number,
            $visit->visitor->full_name,
            $visit->visitor->company_name ?? '-',
            $visit->department->department_name,
            $visit->employee->employee_name,
            $visit->arrival_time?->format('d/m/Y H:i'),
            $visit->exit_time?->format('d/m/Y H:i') ?? '-',
            $duration,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Bold header row
                $event->sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType'   => 'solid',
                        'startColor' => ['argb' => 'FF1E40AF'],
                    ],
                ]);

                // Border semua cell
                $lastRow = $event->sheet->getHighestRow();
                $event->sheet->getStyle('A1:H' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color'       => ['argb' => 'FFCCCCCC'],
                        ],
                    ],
                ]);
            },
        ];
    }
}