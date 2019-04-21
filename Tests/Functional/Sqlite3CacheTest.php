<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use function class_exists;
use function extension_loaded;

/**
 * @group Functional
 * @group Sqlite3
 */
class Sqlite3CacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        if (! extension_loaded('sqlite3')) {
            $this->markTestSkipped('The ' . self::class . ' requires the use of sqlite3');
        }

        if (class_exists('Doctrine\Common\Cache\SQLite3Cache')) {
            return;
        }

        $this->markTestSkipped('The ' . self::class . ' requires the use of SQLite3Cache available in doctrine/cache since 1.4');
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('sqlite3');

        return $container->get('doctrine_cache.providers.my_sqlite3_cache');
    }
}
