<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvacuationCenter extends Model
{
    use HasFactory;

    protected $table = 'evacuation_center';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'barangay_name',
        'latitude',
        'longitude',
        'capacity',
        'status',
        'is_archive'
    ];

    public $timestamps = false;
}
