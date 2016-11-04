<?php

namespace NunoPress\Silex\Cache\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\ProxyAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * Class ConfigServiceProvider
 *
 * @package NunoPress\Silex\Cache\Provider
 */
class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        // Cache object
        $app['cache'] = function (Container $c) {
            $adapter = $c['cache.adapter'];

            if (! $adapter instanceof AbstractAdapter or ! isset($c[ $adapter ])) {
                throw new \Exception('Cache adapter not valid.');
            }

            return (isset($c[ $adapter ])) ? $c[ $adapter ] : $adapter;
        };

        // Basic configuration
        $app['cache.namespace'] = 'sf-cache';

        $app['cache.default_ttl'] = 600;

        // Cache default adapter
        $app['cache.adapter'] = $app['cache.adapters.filesystem'];

        // Apcu adapter
        $app['cache.adapters.apcu.namespace'] = $app['cache.namespace'];

        $app['cache.adapters.apcu.default_ttl'] = $app['cache.default_ttl'];

        $app['cache.adapters.apcu.version'] = 1;

        $app['cache.adapters.apcu'] = function (Container $c) {
            return new ApcuAdapter(
                $c['cache.adapters.apcu.namespace'],
                $c['cache.adapters.apcu.default_ttl'],
                $c['cache.adapters.apcu.version']
            );
        };

        // Array adapter
        $app['cache.adapters.array.default_ttl'] = $app['cache.default_ttl'];

        $app['cache.adapters.array.store_serialized'] = true;

        $app['cache.adapters.array'] = function (Container $c) {
            return new ArrayAdapter(
                $c['cache.adapters.array.default_ttl'],
                $c['cache.adapters.array.store_serialized']
            );
        };

        // Chain adapter
        $app['cache.adapters.chain.default_ttl'] = $app['cache.default_ttl'];

        $app['cache.adapters.chain.adapters'] = [];

        $app['cache.adapters.chain'] = function (Container $c) {
            return new ChainAdapter(
                $c['cache.adapters.chain.adapters'],
                $c['cache.adapters.chain.default_ttl']
            );
        };

        // Doctrine adapter
        $app['cache.adapters.dpctrine.default_ttl'] = $app['cache.default_ttl'];

        $app['cache.adapters.chain.namespace'] = $app['cache.namespace'];

        $app['cache.adapters.doctrine.cache'] = null;

        $app['cache.adapters.doctrine'] = function (Container $c) {
            return new DoctrineAdapter(
                $c['cache.adapters.doctrine.cache'],
                $c['cache.adapters.doctrine.namespace'],
                $c['cache.adapters.doctrine.default_ttl']
            );
        };

        // Filesystem adapter
        $app['cache.adapters.filesystem.namespace'] = $app['cache.namespace'];

        $app['cache.adapters.filesystem.default_ttl'] = $app['cache.default_ttl'];

        $app['cache.adapters.filesystem.directory'] = sys_get_temp_dir() . '/symfony-cache';

        $app['cache.adapters.filesystem'] = function (Container $c) {
            return new FilesystemAdapter(
                $c['cache.adapters.filesystem.namespace'],
                $c['cache.adapters.filesystem.default_ttl'],
                $c['cache.adapters.filesystem.directory']
            );
        };

        // Proxy adapter
        $app['cache.adapters.proxy.namespace'] = $app['cache.namespace'];

        $app['cache.adapters.proxy.default_ttl'] = $app['cache.default_ttl'];

        $app['cache.adapters.proxy.pool'] = null;

        $app['cache.adapters.proxy'] = function (Container $c) {
            return new ProxyAdapter(
                $c['cache.adapters.proxy.pool'],
                $c['cache.adapters.proxy.namespace'],
                $c['cache.adapters.proxy.default_ttl']
            );
        };

        // Redis adapter
        $app['cache.adapters.redis.namespace'] = $app['cache.namespace'];

        $app['cache.adapters.redis.default_ttl'] = $app['cache.default_ttl'];

        $app['cache.adapters.redis.client'] = null;

        $app['cache.adapters.redis'] = function (Container $c) {
            return new RedisAdapter(
                $c['cache.adapters.redis.client'],
                $c['cache.adapters.redis.namespace'],
                $c['cache.adapters.redis.default_ttl']
            );
        };
    }
}