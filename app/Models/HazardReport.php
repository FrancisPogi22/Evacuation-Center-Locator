<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HazardReport extends Model
{
    use HasFactory;

    protected $table = 'hazard_report';

    protected $primaryKey = 'id';

    protected $fillable = [
        'latitude',
        'longitude',
        'type',
        'update',
        'status'
    ];

    public $timestamps = false;
}
