<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusyTable extends Model
{
    use HasFactory;

    public function scopeBusyTablesOnDate($query, $date)
    {
        return $query->where('date', $date);
    }
}
