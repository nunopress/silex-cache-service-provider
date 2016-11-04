<?php

namespace NunoPress\Silex\Cache\Application;

use Pimple\Container;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Cache\Exception\CacheException;

/**
 * Class CacheTrait
 *
 * @package NunoPress\Silex\Cache\Application
 */
trait CacheTrait
{
    /**
     * @param string|CacheItem $key
     *
     * @return mixed
     */
    public function cache($key)
    {
        /** @var AbstractAdapter $cache */
        $cache = $this['cache'];

        if ($key instanceof CacheItem) {
            return $cache->save($key);
        }

        if (true === is_array($key)) {
            return $cache->getItems($key);
        }

        return $cache->getItem($key);
    }

    /**
     * @param string|null $adapter
     *
     * @return AdapterInterface
     *
     * @throws CacheException
     */
    public function caches($adapter = null) {
        /** @var Container $caches */
        $caches = $this['caches'];

        if (null === $adapter) {
            /** @var string $default */
            $default = $this['caches.default'];

            return $caches[ $default ];
        }

        if (! isset($caches[ $adapter ])) {
            throw new CacheException("Adapter {$adapter} not found.");
        }

        return $caches[ $adapter ];
    }
}