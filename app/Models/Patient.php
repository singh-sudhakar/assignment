<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'group', 'wait_time', 'consultation_date', 'patient_id', 'week', 'month',
    ];
}
