<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketProgress extends Model
{
    
    protected $fillable = ["task", "is_solved"];
    protected $casts = [
        "is_solved" => "boolean"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);   
    }

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($model) {
            $model->user_id = auth()->id();
        });
    }
}
