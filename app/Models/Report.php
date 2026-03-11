<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use App\MonthEnum;
class Report extends Model
{
    protected $fillable = ["name", "month", "year"];
}
