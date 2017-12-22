<?php

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

    public function testCacheDriverPrefixSeed()
    {
        $container   = $this->createServiceContainer();
        $definition  = new Definition('%doctrine.orm.cache.apc.class%');
        $cacheDriver = array(
            'type' => 'apc',
            'id'   => 'service_driver'
        );

        $container->setParameter('cache.prefix.seed', 'foo');
        $container->setDefinition('service_driver', $definition);

        $this->adapter->loadCacheDriver('metadata_cache', 'default', $cacheDriver, $container);

        $service = $container->findDefinition('doctrine.orm.default_metadata_cache');

        $expectedMethodCalls = array(
            array(
                'setNamespace',
                array('sf_orm_default_8c36a4de0535c77272fc7390a992fb8c6da987c3b940b2f466ea2596aa31abfb')
            )
        );
        $this->assertSame($expectedMethodCalls, $service->getMethodCalls());
    }

    public function testCacheDriverWithoutPrefixSeed()
    {
        $container   = $this->createServiceContainer();
        $definition  = new Definition('%doctrine.orm.cache.apc.class%');
        $cacheDriver = array(
            'type' => 'apc',
            'id'   => 'service_driver'
        );

        $container->setDefinition('service_driver', $definition);
        $container->setParameter('kernel.root_dir', 'test');

        $this->adapter->loadCacheDriver('metadata_cache', 'default', $cacheDriver, $container);

        $service = $container->findDefinition('doctrine.orm.default_metadata_cache');

        $expectedMethodCalls = array(
            array(
                'setNamespace',
                array('sf_orm_default_b94fa67b19b95498aee2fd6ef50b832b056bd8b4826c3e66209a6e975f48e615')
            )
        );
        $this->assertSame($expectedMethodCalls, $service->getMethodCalls());
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
