<?php

namespace NunoPress\Silex\Cache\Application;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\CacheItem;

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
}