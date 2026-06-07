<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id', 'order_number', 'subtotal', 'tax', 'shipping',
        'total', 'currency', 'status',
        'razorpay_order_id', 'razorpay_payment_id',
        'shipping_address', 'notes', 'paid_at',
    ];

    protected $casts = [
        'subtotal'         => 'decimal:2',
        'tax'              => 'decimal:2',
        'shipping'         => 'decimal:2',
        'total'            => 'decimal:2',
        'shipping_address' => 'array',
        'paid_at'          => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function scopePaid($q) { return $q->where('status', 'paid'); }
}
