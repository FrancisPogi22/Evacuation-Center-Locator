<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportUpdate extends Model
{
    use HasFactory;

    protected $table = 'report_update';

    protected $primaryKey = 'id';

    protected $fillable = [
        'report_id',
        'update_time',
        'update_details'
    ];

    public $timestamps = false;

    public function addUpdate($report_id, $update_details)
    {
        $this->create([
            'report_id'      => $report_id,
            'update_time'    => now(),
            'update_details' => $update_details
        ]);
    }
}
