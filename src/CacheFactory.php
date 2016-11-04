<?php

namespace NunoPress\Silex\Cache;

/**
 * Class CacheFactory
 *
 * @package NunoPress\Silex\Cache
 */
class CacheFactory
{
    /** @var array */
    protected $adapters = [];

    /** @var array */
    protected $options = [
        'namespace' => '',
        'ttl'       => 0
    ];

    /**
     * CacheFactory constructor.
     *
     * @param array $adapters
     * @param array $options
     */
    public function __construct(array $adapters, array $options = [])
    {
        $this->adapters     = $adapters;
        $this->options      = array_merge($this->options, $options);
    }

    /**
     * @param string $adapter
     * @param array $options
     *
     * @return mixed
     */
    public function getCache($adapter, array $options = [])
    {
        /*
        if (! $adapter instanceof AdapterInterface) {
            throw new CacheException('Adapter is not compatible with Symfony\Component\Cache\Adapter\AdapterInterface.');
        }
        */

        $options = array_merge($this->options, $options);

        $class = $this->adapters[ $adapter ];

        switch ($adapter) {
            case 'apcu':
                $options = array_merge([
                    'version'   => null
                ], $options);

                $cache = new $class($options['namespace'], $options['ttl'], $options['version']);

                break;

            case 'array':
                $cache = new $class($options['namespace'], $options['ttl']);

                break;

            case 'chain':
                $options = array_merge([
                    'adapters'  => []
                ], $options);

                $cache = new $class($options['adapters'], $options['ttl']);

                break;

            case 'doctrine':
                $options = array_merge([
                    'provider'  => null
                ], $options);

                $cache = new $class($options['provider'], $options['namespace'], $options['ttl']);

                break;

            case 'filesystem':
                $options = array_merge([
                    'directory' => null
                ], $options);

                $cache = new $class($options['namespace'], $options['ttl'], $options['directory']);

                break;

            case 'proxy':
                $options = array_merge([
                    'pool'      => null
                ], $options);

                $cache = new $class($options['pool'], $options['namespace'], $options['ttl']);

                break;

            case 'redis':
                $options = array_merge([
                    'client'    => null
                ], $options);

                $cache = new $class($options['client'], $options['namespace'], $options['ttl']);

                break;

            default:
                $cache = new $class($options['namespace'], $options['ttl']);

                break;
        }

        return $cache;
    }
}