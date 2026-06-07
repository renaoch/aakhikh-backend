<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'supabase_uid', 'name', 'email', 'phone',
        'avatar_url', 'bio', 'role', 'is_active',
        'email_verified_at', 'last_login_at',
    ];

    protected $hidden = ['remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────

    public function sermons()
    {
        return $this->hasMany(Sermon::class, 'created_by');
    }

    public function dailyBreads()
    {
        return $this->hasMany(DailyBread::class, 'created_by');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'user_id');
    }

    public function submittedTestimonials()
    {
        return $this->hasMany(Testimonial::class, 'submitted_by');
    }

    public function reviewedTestimonials()
    {
        return $this->hasMany(Testimonial::class, 'reviewed_by');
    }

    // ── Helpers ────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
