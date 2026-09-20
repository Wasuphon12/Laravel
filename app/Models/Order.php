<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['total_amount', 'payment_method', 'payment_status', 'transaction_ref', 'payment_slip_path', 'slip_status', 'payment_submitted_at', 'payment_verified_at', 'payment_verified_by', 'shipping_address'];

    protected function casts(): array
    {
        return ['total_amount' => 'decimal:2', 'shipping_address' => 'array', 'payment_submitted_at' => 'datetime', 'payment_verified_at' => 'datetime'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }
}
