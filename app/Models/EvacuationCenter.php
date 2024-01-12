<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvacuationCenter extends Model
{
    use HasFactory;

    protected $table = 'evacuation_center';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'barangay_name',
        'latitude',
        'longitude',
        'status',
        'is_archive',
        'facilities'
    ];

    public $timestamps = false;

    public function getEvacuationCount()
    {
        return EvacuationCenter::select(
            EvacuationCenter::raw('COALESCE(SUM(CASE WHEN `status` = "Active" THEN 1 ELSE 0 END), 0) AS activeEvacuation'),
            EvacuationCenter::raw('COALESCE(SUM(CASE WHEN `status` = "Inactive" THEN 1 ELSE 0 END), 0) AS inactiveEvacuation'),
            EvacuationCenter::raw('COALESCE(SUM(CASE WHEN `status` = "Full" THEN 1 ELSE 0 END), 0) AS fullEvacuation')
        )->first();
    }
}
