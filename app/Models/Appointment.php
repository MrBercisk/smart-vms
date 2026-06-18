<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'appointment_number',
        'appointment_date',
        'appointment_time',
        'visitor_id',
        'employee_id',
        'department_id',
        'purpose',
        'status',
        'rejection_reason',
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
}
