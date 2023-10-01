<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncidentReport extends Model
{
    use HasFactory;

    protected $table = 'incident_report';

    protected $primaryKey = 'id';

    protected $fillable = [
        'description',
        'location',
        'photo',
        'latitude',
        'longitude',
        'status',
        'user_ip',
        'is_archive',
        'report_time'
    ];

    public $timestamps = false;
}
