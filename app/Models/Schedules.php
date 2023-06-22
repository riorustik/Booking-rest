<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    use HasFactory;

    public $shedule_array = ["10:00", "11:00", "12:00", "13:00", "14:00", "15:00"];

    public function addNroll($time, $duration)
    {
       // $this->schedule_array = unset($this->schedule_array)
    }
}
