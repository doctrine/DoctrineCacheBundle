<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @group Functional
 * @group Memcached
 */
class MemcachedCacheTest extends BaseCacheTest
{
    public function testPersistentId()
    {
        $cache = $this->createCacheDriver();
        $this->assertEquals('app', $cache->getMemcached()->getPersistentId());
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        if ( ! extension_loaded('memcached')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of memcached');
        }

        if (@fsockopen('localhost', 11211) === false) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to memcached');
        }
    }

    protected function overrideContainer(ContainerBuilder $container)
    {
        $container->setParameter('doctrine_cache.memcached.connection.class', 'Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Fixtures\Memcached');
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('memcached');
        $cache     = $container->get('doctrine_cache.providers.my_memcached_cache');

        return $cache;
    }
}
