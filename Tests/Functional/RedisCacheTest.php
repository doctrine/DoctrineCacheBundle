<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use Symfony\Component\DependencyInjection\Definition;

/**
 * Redis Driver Test
 *
 * @group Functional
 * @group Redis
 * @author Tomasz Wójcik <tomasz.prgtw.wojcik@gmail.com>
 */
class RedisCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        if ( ! extension_loaded('redis')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the redis extension');
        }

        if (false === @fsockopen('localhost', 6379)) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to redis');
        }

        $container = $this->compileContainer('redis');
        $cache     = $container->get('doctrine_cache.providers.my_redis_cache');

        return $cache;
    }

    /**
     * @dataProvider provideProviders
     *
     * @param string $serviceId
     * @param string $methodUsed
     */
    public function testPersistentConnection($serviceId, $methodUsed)
    {
        $container = $this->compileContainer('redis');
        $definition = $container->getDefinition($serviceId);
        $calls = $definition->getMethodCalls();

        $this->assertCount(1, $calls);
        /** @var Definition $redisDefinition */
        $redisDefinition = $calls[0][1][0];
        $this->assertTrue($redisDefinition->hasMethodCall($methodUsed));
    }

    public function provideProviders()
    {
        return array(
            'not_persistent' => array('doctrine_cache.providers.my_redis_cache', 'connect'),
            'persistent' => array('doctrine_cache.providers.my_persistent_redis_cache', 'pconnect'),
        );
    }
}
