<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DealerProfile extends Model
{
    use HasFactory;

    protected $fillable = ['store_name', 'id_card_url', 'bank_name', 'bank_account', 'promptpay_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DealerReview::class, 'dealer_id', 'user_id');
    }
}
