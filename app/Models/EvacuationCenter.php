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
        'status'
    ];

    public $timestamps = false;

    public function isActive(){
        return $this->where('status', 'Active')->count();
    }

    public function isInactive(){
        return $this->where('status', 'Inactive')->count();
    }

    public function registerEvacuationCenterObject($evacuationCenter){
        return $this->insert($evacuationCenter);
    }

    public function updateEvacuationCenterObject($evacuationCenterData, $evacuationCenterId){
        $evacuationCenter = $this->find($evacuationCenterId);
        $evacuationCenter->update($evacuationCenterData);
    }

    public function removeEvacuationCenterObject($evacuationCenterId){
        $evacuationCenter = $this->find($evacuationCenterId);
        $evacuationCenter->delete();
    }
}
