<?php

namespace NunoPress\Silex\Cache\Provider;

use NunoPress\Silex\Cache\CacheFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
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
 *
 * @see https://github.com/moust/silex-cache-service-provider/blob/master/src/Moust/Silex/Provider/CacheServiceProvider.php
 */
class CacheServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        // Defaults configuration
        $app['cache.default_options'] = [
            'adapter' => 'array'
        ];

        $app['cache.adapters'] = function () {
            return [
                'apcu'          => ApcuAdapter::class,
                'array'         => ArrayAdapter::class,
                'chain'         => ChainAdapter::class,
                'doctrine'      => DoctrineAdapter::class,
                'filesystem'    => FilesystemAdapter::class,
                'proxy'         => ProxyAdapter::class,
                'redis'         => RedisAdapter::class
            ];
        };

        $app['cache.factory'] = $app->factory(function (Container $app) {
            return new CacheFactory($app['cache.adapters'], $app['caches.options']);
        });

        $app['caches.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (! isset($app['caches.options'])) {
                $app['caches.options'] = [
                    'default' => (isset($app['cache.options'])) ? $app['cache.options'] : []
                ];
            }

            $tmp = $app['caches.options'];

            foreach ($tmp as $name => &$options) {
                $options = array_replace($app['cache.default_options'], $options);

                if (! isset($app['caches.default'])) {
                    $app['caches.default'] = $name;
                }
            }

            $app['caches.options'] = $tmp;
        });

        $app['caches'] = $app->factory(function (Container $app) {
            $app['caches.options.initializer']();

            $caches = new Container();

            foreach ($app['caches.options'] as $name => $options) {
                if ($name === $app['caches.default']) {
                    $config = $app['cache.config'];
                } else {
                    $config = $app['caches.config'][$name];
                }

                $caches[ $name ] = $caches->factory(function (Container $caches) use ($app, $config) {
                    return $app['cache.factory']->getCache($config['adapter'], $config);
                });
            }

            return $caches;
        });

        $app['caches.config'] = $app->factory(function (Container $app) {
            $app['caches.options.initializer']();

            $configs = new Container();

            foreach ($app['caches.options'] as $name => $options) {
                $configs[ $name ] = $options;
            }

            return $configs;
        });

        $app['cache'] = $app->factory(function (Container $app) {
            $caches = $app['caches'];

            return $caches[ $app['caches.default'] ];
        });

        $app['cache.config'] = $app->factory(function (Container $app) {
            $configs = $app['caches.config'];

            return $configs[ $app['caches.default'] ];
        });
    }
}