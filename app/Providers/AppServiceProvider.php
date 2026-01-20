<?php

namespace App\Providers;

use App\Core\Contracts\Cache\CacheServiceInterface;
use App\Core\Contracts\Security\OtpServiceInterface;
use App\Core\Infrastructure\Cache\LaravelCacheService;
use App\Core\Infrastructure\Cache\RedisCacheService;
use App\Core\Infrastructure\Security\OtpService;
use App\Core\Infrastructure\Security\RedisOtpService;
use App\Infrastructure\Providers\BookingServiceProvider;
use App\Modules\Authentication\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Authentication\Infrastructure\Repositories\EloquentUserRepository;
use App\Modules\Navigation\Domain\Repositories\MenuItemRepositoryInterface;
use App\Modules\Navigation\Infrastructure\Repositories\EloquentMenuItemRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $myapp = "kollect";

        $this->app->bind(CacheServiceInterface::class, LaravelCacheService::class);

        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(CacheServiceInterface::class, function () use ($myapp) {
            return new RedisCacheService(prefix: $myapp . ':'); // configurable

        });

        $this->app->bind(OtpServiceInterface::class, function () {
            return new OtpService(
                $this->app->make(CacheServiceInterface::class)
            );
        });
        $this->app->bind(MenuItemRepositoryInterface::class, EloquentMenuItemRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blueprint::macro('fk', function (
            string $column,
            string $table,
            string $ref = 'id',
            ?string $name = null
        ) {
            $name ??= "fk_{$this->getTable()}_{$column}";

            return $this->foreign($column, $name)
                ->references($ref)
                ->on($table);
        });
    }
}
