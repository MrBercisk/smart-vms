<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'visitor_code', 'full_name', 'company_name', 'phone',
        'email', 'identity_number', 'identity_type', 'photo', 'status'
    ];
}
