<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'email', 'name', 'confirmation_token',
        'confirmed_at', 'unsubscribed_at', 'unsubscribe_token',
        'source', 'ses_message_id',
    ];

    protected $hidden = ['confirmation_token', 'unsubscribe_token'];

    protected $casts = [
        'confirmed_at'    => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public function scopeActive($q)
    {
        return $q->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }
}
