<!-- resources/views/emails/appointment-rejected.blade.php -->
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Appointment Ditolak</h2>
    <p>Yth. {{ $appointment->visitor->full_name }},</p>
    <p>Mohon maaf, appointment Anda <strong>ditolak</strong>.</p>
    <p><strong>Alasan:</strong> {{ $appointment->rejection_reason }}</p>
</body>
</html>