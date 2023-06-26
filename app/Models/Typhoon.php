<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Typhoon extends Model
{
    use HasFactory;

    protected $table = 'typhoon';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'disaster_id',
        'status'
    ];
}
