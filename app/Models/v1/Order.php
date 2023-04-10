<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, GeneratesUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'payment_id',
        'order_status_id',
        'products',
        'address',
        'delivery_fee',
        'amount',
        'shipped_at',
    ];

    protected $casts = [
        'products' => 'array',
        'address' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
