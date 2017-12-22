<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineCacheBundle\Tests\FunctionalTestCase;

/**
 * @group Functional
 * @group Void
 */
class VoidCacheTest extends BaseCacheTest
{
    public function setUp()
    {
        parent::setUp();

        if (!class_exists('Doctrine\Common\Cache\VoidCache')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of VoidCache available in doctrine/cache since 1.5');
        }
    }

    public function testCacheDriver()
    {
        $cache = $this->createCacheDriver();

        $this->assertNotNull($cache);
        $this->assertInstanceOf('Doctrine\Common\Cache\Cache', $cache);

        $this->assertTrue($cache->save('key', 'value'));
        $this->assertFalse($cache->contains('key'));
        $this->assertFalse($cache->fetch('key'));
        $this->assertTrue($cache->delete('key'));
        $this->assertFalse($cache->contains('key'));
    }

    protected function createCacheDriver()
    {
        $container = $this->compileContainer('void');
        $cache     = $container->get('doctrine_cache.providers.my_void_cache');

        return $cache;
    }
}
