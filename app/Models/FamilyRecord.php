<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyRecord extends Model
{
    use HasFactory;

    protected $table = 'family_record';

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
    ];

    public $timestamps = false;
}
