<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldUnit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sports_field_id',
        'name',
        'index',
        'status',
        'capacity'
    ];

    // ความสัมพันธ์กับสนามหลัก (sports_fields)
    public function field()
    {
        // ใช้ withTrashed() เพื่อให้ยังเข้าถึงได้แม้สนามหลักโดน soft delete ไปแล้ว
        return $this->belongsTo(SportsField::class, 'sports_field_id')->withTrashed();
    }

    // ความสัมพันธ์กับการจอง (bookings)
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'field_unit_id');
    }

    // ความสัมพันธ์กับการปิดสนาม (closures)
    public function closures()
    {
        return $this->hasMany(FieldClosure::class, 'field_unit_id');
    }
}