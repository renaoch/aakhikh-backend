<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title', 'day_of_week', 'start_time', 'end_time',
        'location', 'description', 'is_active', 'display_order',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    public function scopeActive($q) { return $q->where('is_active', true)->orderBy('display_order'); }
}
