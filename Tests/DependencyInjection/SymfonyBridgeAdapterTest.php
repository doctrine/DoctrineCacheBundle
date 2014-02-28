<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\DependencyInjection;

use Doctrine\Bundle\DoctrineCacheBundle\Tests\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\SymfonyBridgeAdapter;
use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\CacheProviderLoader;

/**
 * @group Extension
 * @group SymfonyBridge
 *
 * @author Kinn Coelho Julião <kinncj@php.net>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SymfonyBridgeAdpterTest extends TestCase
{
    /**
     * @var \Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\SymfonyBridgeAdapter
     */
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $this->adapter = new SymfonyBridgeAdapter(new CacheProviderLoader(), 'doctrine.orm', 'orm');
    }

    public function providerBasicDrivers()
    {
        return array(
            array('%doctrine_cache.apc.class%',       array('type' => 'apc')),
            array('%doctrine_cache.array.class%',     array('type' => 'array')),
            array('%doctrine_cache.xcache.class%',    array('type' => 'xcache')),
            array('%doctrine_cache.wincache.class%',  array('type' => 'wincache')),
            array('%doctrine_cache.zenddata.class%',  array('type' => 'zenddata')),
            array('%doctrine_cache.redis.class%',     array('type' => 'redis'),     array('setRedis')),
            array('%doctrine_cache.memcache.class%',  array('type' => 'memcache'),  array('setMemcache')),
            array('%doctrine_cache.memcached.class%', array('type' => 'memcached'), array('setMemcached')),
        );
    }

    /**
     * @param string $class
     * @param array  $config
     *
     * @dataProvider providerBasicDrivers
     */
    public function testLoadBasicCacheDriver($class, array $config, array $expectedCalls = array())
    {
        $container      = $this->createServiceContainer();
        $cacheName      = 'metadata_cache';
        $objectManager  = array(
            'name'                  => 'default',
            'metadata_cache_driver' => $config
        );

        $this->adapter->loadObjectManagerCacheDriver($objectManager, $container, $cacheName);
        $this->assertTrue($container->hasAlias('doctrine.orm.default_metadata_cache'));

        $alias           = $container->getAlias('doctrine.orm.default_metadata_cache');
        $decorator       = $container->getDefinition($alias);
        $definition      = $container->getDefinition($decorator->getParent());
        $defCalls        = $decorator->getMethodCalls();
        $expectedCalls[] = 'setNamespace';
        $actualCalls     = array_map(function ($call) {
            return $call[0];
        }, $defCalls);

        $this->assertEquals($class, $definition->getClass());

        foreach (array_unique($expectedCalls) as $call) {
            $this->assertContains($call, $actualCalls);
        }
    }

    public function testServiceCacheDriver()
    {
        $cacheName      = 'metadata_cache';
        $container      = $this->createServiceContainer();
        $definition     = new Definition('%doctrine.orm.cache.apc.class%');
        $objectManager  = array(
            'name'                  => 'default',
            'metadata_cache_driver' => array(
                'type' => 'service',
                'id'   => 'service_driver'
            )
        );

        $container->setDefinition('service_driver', $definition);

        $this->adapter->loadObjectManagerCacheDriver($objectManager, $container, $cacheName);

        $this->assertTrue($container->hasAlias('doctrine.orm.default_metadata_cache'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage "unrecognized_type" is an unrecognized Doctrine cache driver.
     */
    public function testUnrecognizedCacheDriverException()
    {
        $cacheName      = 'metadata_cache';
        $container      = $this->createServiceContainer();
        $objectManager  = array(
            'name'                  => 'default',
            'metadata_cache_driver' => array(
                'type' => 'unrecognized_type'
            )
        );

        $this->adapter->loadObjectManagerCacheDriver($objectManager, $container, $cacheName);
    }

    public function testLoadServicesConfiguration()
    {
        $container = $this->createContainer();

        $this->assertFalse($container->hasParameter('doctrine_cache.array.class'));
        $this->adapter->loadServicesConfiguration($container);
        $this->assertTrue($container->hasParameter('doctrine_cache.array.class'));
    }
}