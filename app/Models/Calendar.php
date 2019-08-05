<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

class Calendar extends Model
{
    public $table = 'calendar_events';

    public $fillable = [
        'event_name',
        'start_date',
        'end_date',
        'week_days'
    ];

    protected $casts = [

    ];

    public static $rules = [

    ];
}
