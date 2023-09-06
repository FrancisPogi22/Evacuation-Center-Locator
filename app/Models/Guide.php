<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guide extends Model
{
    use HasFactory;

    protected $table = 'guide';

    protected $primaryKey = 'id';

    protected $fillable = [
        'label',
        'content',
        'guideline_id',
        'user_id',
        'guide_photo'
    ];

    public $timestamps = false;
}
