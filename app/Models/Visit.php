<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
   protected $fillable = [
        'visit_number',
        'visitor_id',
        'employee_id',
        'department_id',
        'appointment_id',
        'purpose',
        'visitor_photo',
        'identity_photo',
        'qr_code',
        'arrival_time',
        'exit_time',
        'duration_minutes',
        'status',
    ];

    protected $casts = [
        'arrival_time' => 'datetime',
        'exit_time'    => 'datetime',
    ];

    public function visitor() {
        return $this->belongsTo(Visitor::class);
    }
    public function employee() {
        return $this->belongsTo(Employee::class);
    }
    public function department() {
        return $this->belongsTo(Department::class);
    }
    public function appointment() {
        return $this->belongsTo(Appointment::class);
    }

    // Helper: format durasi jadi "2 jam 15 menit"
    public function getDurationLabelAttribute(): string
    {
        if (!$this->duration_minutes) return '-';
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;
        if ($hours > 0 && $minutes > 0) return "{$hours} jam {$minutes} menit";
        if ($hours > 0) return "{$hours} jam";
        return "{$minutes} menit";
    }
}
