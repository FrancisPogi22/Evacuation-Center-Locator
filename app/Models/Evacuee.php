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
        'individuals',
        'male',
        'female',
        'family_head',
        'birth_date',
        'barangay',
        'family_id',
        'disaster_id',
        'evacuation_id',
        'status',
        'is_archive',
        'updated_at'
    ];

    public $timestamps = false;
}
