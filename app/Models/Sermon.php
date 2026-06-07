<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sermon extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'youtube_video_id', 'title', 'speaker', 'topic',
        'description', 'thumbnail_url', 'published_at',
        'duration_seconds', 'is_featured', 'views_count',
        'is_active', 'is_manual_override', 'created_by',
    ];

    protected $casts = [
        'published_at'       => 'datetime',
        'is_featured'        => 'boolean',
        'is_active'          => 'boolean',
        'is_manual_override' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($q)    { return $q->where('is_active', true); }
    public function scopeFeatured($q)  { return $q->where('is_featured', true); }
}
