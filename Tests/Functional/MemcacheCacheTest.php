<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use function extension_loaded;
use function fsockopen;

/**
 * @group Functional
 * @group Memcache
 */
class MemcacheCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        if (! extension_loaded('memcache')) {
            $this->markTestSkipped('The ' . self::class . ' requires the use of memcache');
        }

        if (@fsockopen('localhost', 11211) !== false) {
            return;
        }

        $this->markTestSkipped('The ' . self::class . ' cannot connect to memcache');
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('memcache');

        return $container->get('doctrine_cache.providers.my_memcache_cache');
    }
}
