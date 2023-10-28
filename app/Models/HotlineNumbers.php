<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotlineNumbers extends Model
{
    use HasFactory;

    protected $table = 'hotline_numbers';

    protected $primaryKey = 'id';

    protected $fillable = [
        'label',
        'number',
        'logo',
        'user_id'
    ];

    public $timestamps = false;
}
