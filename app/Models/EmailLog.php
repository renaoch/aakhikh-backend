<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'daily_bread_id', 'subscriber_id', 'recipient_email',
        'subject', 'status', 'ses_message_id',
        'sent_at', 'failed_at', 'failure_reason',
    ];

    protected $casts = [
        'sent_at'   => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function dailyBread()
    {
        return $this->belongsTo(DailyBread::class, 'daily_bread_id');
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_id');
    }
}
