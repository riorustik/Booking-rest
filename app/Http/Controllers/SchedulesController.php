<?php

namespace App\Http\Controllers;

use App\Models\Schedules;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    //

    public function test()
    {
        $schedule = new Schedules();
        $schedule->date = Carbon::now();
        $schedule->table_id =3;
        $schedule->schedule_array = ["10:00", "11:00", "12:00", "13:00", "14:00", "15:00"];
        $schedule->save();

    }

}
