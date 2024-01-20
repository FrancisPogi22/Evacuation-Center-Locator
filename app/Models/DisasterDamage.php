<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisasterDamage extends Model
{
    use HasFactory;

    protected $table = 'disaster_damage';

    protected $primaryKey = 'id';

    protected $fillable = [
        'description',
        'quantity',
        'cost',
        'barangay',
        'disaster_id'
    ];

    public $timestamps = false;
}
