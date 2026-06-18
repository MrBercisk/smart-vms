<!-- resources/views/emails/appointment-approved.blade.php -->
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Appointment Disetujui</h2>
    <p>Yth. {{ $appointment->visitor->full_name }},</p>
    <p>Appointment Anda telah <strong>disetujui</strong>.</p>
    <table border="1" cellpadding="8" style="border-collapse:collapse">
        <tr><td>No. Appointment</td><td>{{ $appointment->appointment_number }}</td></tr>
        <tr><td>Tanggal</td><td>{{ $appointment->appointment_date }}</td></tr>
        <tr><td>Jam</td><td>{{ $appointment->appointment_time }}</td></tr>
        <tr><td>Keperluan</td><td>{{ $appointment->purpose }}</td></tr>
        <tr><td>Host</td><td>{{ $appointment->employee->employee_name }}</td></tr>
        <tr><td>Departemen</td><td>{{ $appointment->department->department_name }}</td></tr>
    </table>
    <p>Mohon hadir tepat waktu.</p>
</body>
</html>