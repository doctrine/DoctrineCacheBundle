<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

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

        if ( ! extension_loaded('memcache')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of memcache');
        }

        if (@fsockopen('localhost', 11211) === false) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to memcache');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('memcache');
        $cache     = $container->get('doctrine_cache.providers.my_memcache_cache');

        return $cache;
    }
}
