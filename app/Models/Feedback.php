<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    protected $primaryKey = 'id';

    protected $fillable = [
        'feedback',
        'evacuation_center_id'
    ];

    public $timestamps = false;
}
