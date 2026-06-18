<!-- resources/views/emails/visitor-arrived.blade.php -->
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Notifikasi Kedatangan Tamu</h2>
    <p>Yth. {{ $visit->employee->employee_name }},</p>
    <p>Tamu berikut telah tiba di resepsionis:</p>
    <table border="1" cellpadding="8" style="border-collapse:collapse">
        <tr><td>No. Kunjungan</td><td>{{ $visit->visit_number }}</td></tr>
        <tr><td>Nama Tamu</td><td>{{ $visit->visitor->full_name }}</td></tr>
        <tr><td>Perusahaan</td><td>{{ $visit->visitor->company_name ?? '-' }}</td></tr>
        <tr><td>Keperluan</td><td>{{ $visit->purpose }}</td></tr>
        <tr><td>Waktu Tiba</td><td>{{ $visit->arrival_time->format('d/m/Y H:i') }}</td></tr>
    </table>
    <p>Mohon segera menemui tamu Anda.</p>
</body>
</html>