<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SportsField extends Model
{   
    use SoftDeletes;
    protected $fillable = [
        'name','sport_type','location','capacity','status',
        'owner_id','min_duration_minutes','max_duration_minutes','lead_time_hours',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings() 
    { 
        return $this->hasManyThrough(Booking::class, FieldUnit::class, 'sports_field_id', 'field_unit_id'); 
    }

    public function fieldClosures(): HasMany
    {
        return $this->hasMany(FieldClosure::class);
    }
    public function units() 
    { 
        return $this->hasMany(FieldUnit::class); 
    }

}