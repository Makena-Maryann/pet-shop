<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Services\Jwt\SystemClock;
use App\Services\Jwt\TokenGeneration;
use Lcobucci\JWT\Signer\Key\InMemory;
use App\Services\Jwt\TokenVerification;
use Illuminate\Support\ServiceProvider;
use App\Services\Jwt\Interfaces\TokenVerifier;
use App\Services\Jwt\Interfaces\TokenGenerator;

class TokenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TokenGenerator::class, function ($app) {
            return new TokenGeneration(
                config('jwt.url'),
                InMemory::file(config('jwt.private_key_path')),
                config('jwt.algorithm'),
                Carbon::now()->addMinutes(config('jwt.expires_at'))->toDateTimeImmutable(),
                Carbon::now()->addMinutes(config('issued_at'))->toDateTimeImmutable(),
                Carbon::now()->addMinutes(config('used_after'))->toDateTimeImmutable(),
            );
        });

        $this->app->bind(TokenVerifier::class, function ($app) {
            return new TokenVerification(
                config('jwt.url'),
                InMemory::file(config('jwt.public_key_path')),
                config('jwt.algorithm'),
                new SystemClock()
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
