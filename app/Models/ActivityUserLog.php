<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityUserLog extends Model
{
    use HasFactory;

    protected $table = 'activity_log';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'activity',
        'log_time'
    ];

    public $timestamps = false;

    public function generateLog($activity)
    {
        $this->create([
            'user_id' => auth()->user()->id,
            'activity'  => trim($activity),
            'log_time' => now()->toDayDateTimeString()
        ]);
    }
}
