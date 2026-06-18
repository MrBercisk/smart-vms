<!-- resources/views/pdf/visitor-pass.blade.php -->
<!DOCTYPE html>
<html>
<head>
<style>
    body { font-family: Arial, sans-serif; }
    .card {
        border: 2px solid #333;
        border-radius: 8px;
        padding: 20px;
        max-width: 400px;
        margin: 0 auto;
    }
    .header {
        text-align: center;
        background: #1e40af;
        color: white;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 16px;
    }
    .row { margin-bottom: 8px; }
    .label { font-size: 11px; color: #666; }
    .value { font-size: 14px; font-weight: bold; }
    .qr { text-align: center; margin-top: 16px; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h2 style="margin:0">VISITOR PASS</h2>
        <p style="margin:4px 0 0">{{ $visit->visit_number }}</p>
    </div>

    <div class="row">
        <div class="label">Nama Tamu</div>
        <div class="value">{{ $visit->visitor->full_name }}</div>
    </div>
    <div class="row">
        <div class="label">Perusahaan</div>
        <div class="value">{{ $visit->visitor->company_name ?? '-' }}</div>
    </div>
    <div class="row">
        <div class="label">Keperluan</div>
        <div class="value">{{ $visit->purpose }}</div>
    </div>
    <div class="row">
        <div class="label">Host / Karyawan</div>
        <div class="value">{{ $visit->employee->employee_name }}</div>
    </div>
    <div class="row">
        <div class="label">Departemen</div>
        <div class="value">{{ $visit->department->department_name }}</div>
    </div>
    <div class="row">
        <div class="label">Tanggal Kunjungan</div>
        <div class="value">{{ $visit->arrival_time->format('d/m/Y H:i') }}</div>
    </div>

    <div class="qr">
        {!! $qr !!}
    </div>
</div>
</body>
</html>