<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{   

    protected $fillable = [
        'user_id',
        'sports_field_id',
        'field_unit_id',  
        'date',
        'start_time',
        'end_time',
        'status',
        'purpose',
        'contact_phone',
    ];

    protected $casts = [
        'date'        => 'date',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sportsField() 
    { 
        return $this->belongsTo(SportsField::class, 'sports_field_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BookingLog::class);
    }
    
    public function unit() 
    { 
        return $this->belongsTo(FieldUnit::class, 'field_unit_id'); 
    }
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

}