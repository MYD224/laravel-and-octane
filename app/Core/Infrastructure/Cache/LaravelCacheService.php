<?php

namespace App\Core\Infrastructure\Cache;

use App\Core\Contracts\Cache\CacheServiceInterface;
use Illuminate\Support\Facades\Cache;

class LaravelCacheService implements CacheServiceInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    public function set(string $key, mixed $value, int $ttl = 3600): void
    {
        Cache::put($key, $value, $ttl);
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    public function delete(string $key): void
    {
        Cache::forget($key);
    }

    public function clear(): void
    {
        Cache::flush();
    }
}
