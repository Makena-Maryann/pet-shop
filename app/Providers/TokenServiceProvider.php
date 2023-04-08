<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Services\TokenService;
use Lcobucci\JWT\Signer\Key\InMemory;
use Illuminate\Support\ServiceProvider;

class TokenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TokenService::class, function ($app) {
            return new TokenService(
                config('jwt.url'),
                InMemory::file(config('jwt.private_key_path')),
                InMemory::file(config('jwt.public_key_path')),
                config('jwt.algorithm'),
                Carbon::now()->addMinutes(config('jwt.expires_at'))->toDateTimeImmutable(),
                Carbon::now()->addMinutes(config('issued_at'))->toDateTimeImmutable(),
                Carbon::now()->addMinutes(config('used_after'))->toDateTimeImmutable(),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
