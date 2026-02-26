<?php

namespace App\Models;

use App\TicketStatus;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    protected $fillable = [
        'pelanggan_id',
        'title',
        'description',
        'uuid',
        'solver',
        'status',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function solved_by()
    {
        return $this->belongsTo(User::class, "solver", "id");
    }
    public function progress()
    {
        return $this->hasMany(TicketProgress::class)
            ->latest();
    }
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid(); //v4
            $model->user_id = auth()->id();
        });
    }
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
    protected $casts = [
        'status' => TicketStatus::class,
    ];
}
