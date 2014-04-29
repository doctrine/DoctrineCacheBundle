<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Logger;

use Doctrine\Bundle\DoctrineCacheBundle\Logger\CacheLoggerProxy;

/**
 * Unit tests for CacheLoggerProxy.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class CacheLoggerProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\Bundle\DoctrineCacheBundle\Logger\CacheLoggerProxy
     */
    private $proxy;

    /**
     * @var string
     */
    private $id = 'test_cache_id';

    /**
     * @var string
     */
    private $invalidId = 'invalid_id';

    /**
     * @var string
     */
    private $data = 'hello world';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $serviceId   = 'doctrine_cache.providers.test';
        $class       = '\\Doctrine\\Common\\Cache\\ArrayCache';
        $args        = array();
        $this->proxy = new CacheLoggerProxy($serviceId, $class, $args);

        $this->proxy->setLogMaster($this->getMock('\\Doctrine\\Bundle\\DoctrineCacheBundle\\Logger\\LogMaster'));
    }

    /**
     * Test writing a cache entry.
     */
    public function testSave()
    {
        $result = $this->proxy->save($this->id, $this->data, 5);
        $this->assertTrue($result);
    }

    /**
     * Test retrieving a valid cache entry.
     */
    public function testFetchHit()
    {
        $this->proxy->save($this->id, $this->data, 5);
        $result = $this->proxy->fetch($this->id);
        $this->assertEquals($this->data, $result);
    }

    /**
     * Test retrieving an invalid cache entry.
     */
    public function testFetchMiss()
    {
        $this->proxy->save($this->id, $this->data, 5);
        $result = $this->proxy->fetch($this->invalidId);
        $this->assertFalse($result);
    }

    /**
     * Test deleting a cache entry.
     */
    public function testDelete()
    {
        $result = $this->proxy->delete($this->id);
        $this->assertTrue($result);
    }
}
