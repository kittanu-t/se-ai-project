<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldClosure extends Model
{
    protected $fillable = ['sports_field_id','start_datetime','end_datetime','reason','created_by'];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
    ];

    public function unit() 
    { 
        return $this->belongsTo(FieldUnit::class, 'field_unit_id'); 
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}