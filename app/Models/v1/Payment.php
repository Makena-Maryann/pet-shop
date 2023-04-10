<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, GeneratesUuid;

    protected $fillable = [
        'uuid',
        'type',
        'details'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getDetailsAttribute($value): array
    {
        return json_decode($value);
    }

    public function setDetailsAttribute($value): void
    {
        $this->attributes['details'] = json_encode($value);
    }
}
