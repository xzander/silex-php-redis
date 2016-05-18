<?php

namespace SilexPhpRedis;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PhpRedisProvider implements ServiceProviderInterface
{
    const REDIS = 'redis';
    const REDIS_OPTIONS = 'redis.options';

    const OPT_HOST = 'host';
    const OPT_PORT = 'port';
    const OPT_TIMEOUT = 'timeout';
    const OPT_PERSISTENT = 'persistent';
    const OPT_AUTH = 'auth';
    const OPT_SERIALIZER_IG_BINARY = 'serializer.igbinary';
    const OPT_SERIALIZER_PHP = 'serializer.php';
    const OPT_PREFIX = 'prefix';
    const OPT_DATABASE = 'database';

    protected static $defaultOptions = array(
        self::OPT_HOST => array(),
        self::OPT_PORT => 6379,
    );

    public function boot(Container $app)
    {

    }

    public function register(Container $container)
    {
        $defaultOptions = self::$defaultOptions;

        $container[self::REDIS] = function () use ($container, $defaultOptions) {
            $options = array_merge($defaultOptions, $container[PhpRedisProvider::REDIS_OPTIONS]);
            $thisRedis = new \Redis();
            $host = isset($options[PhpRedisProvider::OPT_HOST]) ? $options[PhpRedisProvider::OPT_HOST] : array();
            $port = isset($options[PhpRedisProvider::OPT_PORT]) && is_int($options[PhpRedisProvider::OPT_PORT]) ? $options[PhpRedisProvider::OPT_PORT] : 6379;
            $timeout = isset($options[PhpRedisProvider::OPT_TIMEOUT]) && is_int($options[PhpRedisProvider::OPT_TIMEOUT]) ? $options[PhpRedisProvider::OPT_TIMEOUT] : 0;
            $persistent = isset($options[PhpRedisProvider::OPT_PERSISTENT]) ? $options[PhpRedisProvider::OPT_PERSISTENT] : false;
            $auth = isset($options[PhpRedisProvider::OPT_AUTH]) ? $options[PhpRedisProvider::OPT_AUTH] : null;
            $serializerIgbinary = isset($options[PhpRedisProvider::OPT_SERIALIZER_IG_BINARY]) ? $options[PhpRedisProvider::OPT_SERIALIZER_IG_BINARY] : false;
            $serializerPhp = isset($options[PhpRedisProvider::OPT_SERIALIZER_PHP]) ? $options[PhpRedisProvider::OPT_SERIALIZER_PHP] : false;
            $prefix = isset($options[PhpRedisProvider::OPT_PREFIX]) ? $options[PhpRedisProvider::OPT_PREFIX] : null;
            $database = isset($options[PhpRedisProvider::OPT_DATABASE]) ? $options[PhpRedisProvider::OPT_DATABASE] : null;

            if ($persistent) {
                $thisRedis->pconnect($host, $port, $timeout);
            } else {
                $thisRedis->connect($host, $port, $timeout);
            }

            if (!empty($auth)) {
                $thisRedis->auth($auth);
            }

            if ($database) {
                $thisRedis->select($database);
            }

            if ($serializerIgbinary) {
                $thisRedis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
            }

            if ($serializerPhp) {
                $thisRedis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            }

            if ($prefix) {
                $thisRedis->setOption(\Redis::OPT_PREFIX, $prefix);
            }

            return $thisRedis;
        };
    }
}
