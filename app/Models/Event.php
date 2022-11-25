<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'change_at',
        'lead_id',
        'status_at',
        'status_to',
        'pipeline_at',
        'pipeline_to',
    ];
}
