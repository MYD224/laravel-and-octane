<?php

namespace App\Core\Infrastructure\Cache;

use App\Core\Contracts\Cache\CacheServiceInterface;
use Illuminate\Support\Facades\Redis;

class RedisCacheService implements CacheServiceInterface
{
    private string $prefix;

    public function __construct(string $prefix = 'app:')
    {
        $this->prefix = rtrim($prefix, ':') . ':';
    }

    /**
     * Build full redis key with prefix.
     */
    private function key(string $key): string
    {
        return $this->prefix . $key;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = Redis::get($this->key($key));

        return $value !== null ? unserialize($value) : $default;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): void
    {
        Redis::setex(
            $this->key($key),
            $ttl,
            serialize($value)
        );
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $fullKey = $this->key($key);
        $existing = Redis::get($fullKey);

        if ($existing !== null) {
            return unserialize($existing);
        }

        $value = $callback();

        Redis::setex($fullKey, $ttl, serialize($value));

        return $value;
    }

    public function has(string $key): bool
    {
        return Redis::exists($this->key($key)) > 0;
    }

    public function delete(string $key): void
    {
        Redis::del($this->key($key));
    }

    public function clear(): void
    {
        $pattern = $this->key('*');
        $keys = Redis::keys($pattern);

        if (!empty($keys)) {
            Redis::del($keys);
        }
    }

    /**
     * Tagging – simple version: maintain a set for each tag.
     */
    public function rememberTag(string $tag, string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->remember($key, $ttl, $callback);

        Redis::sadd($this->prefix . 'tag:' . $tag, $this->key($key));

        return $value;
    }

    public function flushTag(string $tag): void
    {
        $tagSet = $this->prefix . 'tag:' . $tag;

        $keys = Redis::smembers($tagSet);

        if (!empty($keys)) {
            Redis::del($keys);
        }

        Redis::del($tagSet);
    }
}
