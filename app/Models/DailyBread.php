<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyBread extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title', 'body', 'bible_reference', 'image_url',
        'published_date', 'scheduled_sent_at', 'is_published', 'created_by',
    ];

    protected $casts = [
        'published_date'   => 'date',
        'scheduled_sent_at'=> 'datetime',
        'is_published'     => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'daily_bread_id');
    }

    public function scopePublished($q) { return $q->where('is_published', true); }
}
