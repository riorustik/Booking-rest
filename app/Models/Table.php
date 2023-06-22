<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Builder;

class Table extends Model
{
    use HasFactory;

//    public function scopeFreeTables($query, $date, $guest_quantity){
//
//        $busyTables = BusyTable::where('date', $date)->
//        get()->pluck('table_id');
//
//        return $query->whereNotIn('id', $busyTables)->where('guest_quantity', '>=', $guest_quantity) ;
//    }
    public function scopeFreeTablesBusy($query,  $date, $guest_quantity,$st, $end ){

        $busyTables = BusyTable::where('date', $date)
            ->where(function($query) use ($st, $end) {
                $query->where('time_end', '>', $st)
                    ->orWhere('time_start', '<',$end );
            })->
        get()->pluck('table_id');

        return $query->whereNotIn('id', $busyTables)->where('guest_quantity', '>=', $guest_quantity) ;
       // return $query->whereIn('id', $busyTables)->where('guest_quantity', '>=', $guest_quantity);

    }
}
