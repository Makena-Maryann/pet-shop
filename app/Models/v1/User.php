<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory, GeneratesUuid;

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'is_admin',
        'email',
        'password',
        'email_verified_at',
        'avatar',
        'address',
        'phone_number',
        'is_marketing',
        'last_login_at',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_marketing' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
