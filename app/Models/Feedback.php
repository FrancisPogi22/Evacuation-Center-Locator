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
        'clean_facilities',
        'responsive_aid',
        'safe_evacutaion',
        'sufficient_food_supply',
        'comfortable_evacuation',
        'well_managed_evacuation',
        'evacuation_center_id'
    ];

    public $timestamps = false;
}
