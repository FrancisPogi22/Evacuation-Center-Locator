<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evacuee extends Model
{
    use HasFactory;

    protected $table = 'evacuee';

    protected $primaryKey = 'id';

    protected $fillable = [
        'infants',
        'minors',
        'senior_citizen',
        'pwd',
        'pregnant',
        'lactating',
        'families',
        'individuals',
        'male',
        'female',
        'disaster_id',
        'date_entry',
        'barangay',
        'evacuation_assigned',
        'remarks',
        'is_archive'
    ];

    public $timestamps = false;
}
