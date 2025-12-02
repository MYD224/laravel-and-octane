<?php

namespace App\Providers;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Core\Contracts\Security\OtpServiceInterface;
use App\Core\Infrastructure\Cache\LaravelCacheService;
use App\Core\Infrastructure\Cache\RedisCacheService;
use App\Core\Infrastructure\Security\RedisOtpService;
use App\Infrastructure\Providers\BookingServiceProvider;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Authentication\Infrastructure\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $myapp = "beetrans";

        $this->app->bind(CacheServiceInterface::class, LaravelCacheService::class);

        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(CacheServiceInterface::class, function () use($myapp) {
            return new RedisCacheService(prefix: $myapp.':'); // configurable

        });

        $this->app->bind(OtpServiceInterface::class, function (){
            return new RedisOtpService(prefix: "otp:");
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
          
    }
}
