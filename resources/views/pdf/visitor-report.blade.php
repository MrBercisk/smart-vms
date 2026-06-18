<!-- resources/views/pdf/visitor-report.blade.php -->
<!DOCTYPE html>
<html>
<head>
<style>
    body { font-family: Arial, sans-serif; font-size: 11px; }
    h2 { text-align: center; margin-bottom: 4px; }
    p  { text-align: center; margin: 0 0 12px; color: #666; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #1e40af; color: white; padding: 6px 8px; text-align: left; }
    td { padding: 5px 8px; border-bottom: 1px solid #ddd; }
    tr:nth-child(even) td { background: #f5f5f5; }
    .total { margin-top: 12px; font-weight: bold; }
</style>
</head>
<body>
    <h2>Laporan Kunjungan Tamu</h2>
    <p>Periode: {{ $start_date }} s/d {{ $end_date }}</p>

    <table>
        <thead>
            <tr>
                <th>No. Kunjungan</th>
                <th>Nama Tamu</th>
                <th>Perusahaan</th>
                <th>Departemen</th>
                <th>Host</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Durasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
            @php
                $hours   = intdiv($visit->duration_minutes ?? 0, 60);
                $minutes = ($visit->duration_minutes ?? 0) % 60;
                $duration = $visit->duration_minutes ? "{$hours}j {$minutes}m" : '-';
            @endphp
            <tr>
                <td>{{ $visit->visit_number }}</td>
                <td>{{ $visit->visitor->full_name }}</td>
                <td>{{ $visit->visitor->company_name ?? '-' }}</td>
                <td>{{ $visit->department->department_name }}</td>
                <td>{{ $visit->employee->employee_name }}</td>
                <td>{{ $visit->arrival_time?->format('d/m/Y H:i') }}</td>
                <td>{{ $visit->exit_time?->format('d/m/Y H:i') ?? '-' }}</td>
                <td>{{ $duration }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total Kunjungan: {{ $visits->count() }}</p>
</body>
</html>