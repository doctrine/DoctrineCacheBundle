<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineCacheBundle\Tests\FunctionalTestCase;
use Doctrine\Common\Cache\Cache;

/**
 * @group Functional
 */
abstract class BaseCacheTest extends FunctionalTestCase
{
    /**
     * @return Cache
     */
    abstract protected function createCacheDriver();

    public function testCacheDriver()
    {
        $cache = $this->createCacheDriver();

        $this->assertNotNull($cache);
        $this->assertInstanceOf('Doctrine\Common\Cache\Cache', $cache);

        $this->assertTrue($cache->save('key', 'value'));
        $this->assertTrue($cache->contains('key'));
        $this->assertEquals('value', $cache->fetch('key'));
        $this->assertTrue($cache->delete('key'));
        $this->assertFalse($cache->contains('key'));
    }
}
