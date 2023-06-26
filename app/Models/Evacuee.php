<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evacuee extends Model
{
    use HasFactory;

    protected $table = 'evacuee';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $fillable = [
        'house_hold_number',
        'full_name',
        'sex',
        'age',
        '4Ps',
        'PWD',
        'pregnant',
        'lactating',
        'student',
        'working',
        'barangay',
        'date_entry',
        'date_out',
        'disaster_type',
        'disaster_id',
        'disaster_info',
        'evacuation_assigned'
    ];

    public $timestamps = false;
    
    public function countEvacuee($disaster, $sex)
    {
        return $this->where('disaster_type', $disaster)->where('sex', $sex)->count();
    }

    public function countEvacueeWithDisablities($disaster)
    {
        return $this->selectRaw('SUM(`4Ps`) AS `4Ps`, SUM(`PWD`) AS `PWD`, SUM(`pregnant`) AS `pregnant`, SUM(`lactating`) AS `lactating`, SUM(`student`) AS `student`, SUM(`working`) AS `working`')->where('disaster_type', $disaster)->get();
    }

    public function updateEvacueeDateOut($id, $dateout)
    {
        $this->find($id)->update($dateout);
    }
}
