<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getDetailsAttribute($value)
    {
        return json_decode($value);
    }

    public function setDetailsAttribute($value)
    {
        $this->attributes['details'] = json_encode($value);
    }
}
