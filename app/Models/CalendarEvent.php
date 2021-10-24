<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $table = "calendar_event";
    // public $timestamps = true;

    protected $fillable = [
		'event_date',
        'event_name'
	];
}
