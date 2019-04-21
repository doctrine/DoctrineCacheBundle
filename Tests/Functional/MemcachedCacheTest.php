<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use function extension_loaded;
use function fsockopen;

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

        if (! extension_loaded('memcached')) {
            $this->markTestSkipped('The ' . self::class . ' requires the use of memcached');
        }

        if (@fsockopen('localhost', 11211) !== false) {
            return;
        }

        $this->markTestSkipped('The ' . self::class . ' cannot connect to memcached');
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

        return $container->get('doctrine_cache.providers.my_memcached_cache');
    }
}
