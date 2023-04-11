<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'is_admin',
        'password',
        'remember_token',
    ];

    /**
     * Attributes that should be appended.
     *
     * @return string
     */
    protected $appends = [
        'name',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(JwtToken::class, 'user_id');
    }

    /**
     * Get the user's full name.
     */
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            $user->password = bcrypt($user->password);
        });
    }

    public function scopeFilterBy($query, $filters)
    {
        $query->when(isset($filters['first_name']), function ($query) use ($filters) {
            $query->where('first_name', 'like', '%' . $filters['first_name'] . '%');
        });

        $query->when(isset($filters['email']), function ($query) use ($filters) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        });

        $query->when(isset($filters['phone']), function ($query) use ($filters) {
            $query->where('phone_number', 'like', '%' . $filters['phone'] . '%');
        });

        $query->when(isset($filters['address']), function ($query) use ($filters) {
            $query->where('address', 'like', '%' . $filters['address'] . '%');
        });

        $query->when(isset($filters['created_at']), function ($query) use ($filters) {
            $query->where('created_at', 'like', '%' . $filters['created_at'] . '%');
        });

        $query->when(isset($filters['marketing']), function ($query) use ($filters) {
            $query->where('is_marketing', $filters['marketing']);
        });
    }
}
