<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guideline extends Model
{
    use HasFactory;

    protected $table = 'guideline';

    protected $primaryKey = 'id';

    protected $fillable = [
        'type',
        'organization',
        'cover_image',
        'content_image'
    ];

    public $timestamps = false;
}
