<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flashflood extends Model
{
    use HasFactory;

    protected $table = 'flashflood';

    protected $primaryKey = 'id';

    protected $fillable = [
        'location',
        'disaster_id',
        'status',
        'longitude',
        'latitude',
    ];
}
