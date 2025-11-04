<?php
namespace App\Traits;

use Hyperf\Cache\Cache;


trait UsingCache
{
    public function __construct(protected Cache $cache) {
    }

    public function getCache(string $key)
    {
        return $this->cache->get($key);
    }

    public function setCache(string $key, $value, int $ttl = 3600): void
    {
        $this->cache->set($key, $value, $ttl);
    }

    public function clearCache(string $key): void
    {
        $this->cache->delete($key);
    }
}