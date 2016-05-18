<?php

require __DIR__.'/../vendor/autoload.php';

use SilexPhpRedis\PhpRedisProvider;

$app = new Silex\Application();

$app->register(new PhpRedisProvider(), array(
    PhpRedisProvider::REDIS_OPTIONS => array(
        PhpRedisProvider::OPT_HOST => '127.0.0.1',
        PhpRedisProvider::OPT_PORT => 6379,
        PhpRedisProvider::OPT_TIMEOUT => 30,
        PhpRedisProvider::OPT_PERSISTENT => true,
        PhpRedisProvider::OPT_SERIALIZER_IG_BINARY => false, // use igBinary serialize/unserialize
        PhpRedisProvider::OPT_SERIALIZER_PHP => false, // use built-in serialize/unserialize
        PhpRedisProvider::OPT_PREFIX => 'myprefix',
        PhpRedisProvider::OPT_DATABASE => '0'
    )
));

/** routes **/
$app->get('/', function () use ($app) {
    return var_export($app[PhpRedisProvider::REDIS]->info(), true);
});

/** run application **/
$app->run();