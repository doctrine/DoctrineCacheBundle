<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use Symfony\Component\DependencyInjection\Definition;
use function extension_loaded;
use function fsockopen;

/**
 * Redis Driver Test
 *
 * @group Functional
 * @group Redis
 */
class RedisCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        if (! extension_loaded('redis')) {
            $this->markTestSkipped('The ' . self::class . ' requires the redis extension');
        }

        if (@fsockopen('localhost', 6379) === false) {
            $this->markTestSkipped('The ' . self::class . ' cannot connect to redis');
        }

        $container = $this->compileContainer('redis');

        return $container->get('doctrine_cache.providers.my_redis_cache');
    }

    /**
     * @param string $serviceId
     * @param string $methodUsed
     *
     * @dataProvider provideProviders
     */
    public function testPersistentConnection($serviceId, $methodUsed)
    {
        $container  = $this->compileContainer('redis');
        $definition = $container->getDefinition($serviceId);
        $calls      = $definition->getMethodCalls();

        $this->assertCount(1, $calls);
        /** @var Definition $redisDefinition */
        $redisDefinition = $calls[0][1][0];
        $this->assertTrue($redisDefinition->hasMethodCall($methodUsed));
    }

    public function provideProviders()
    {
        return [
            'not_persistent' => ['doctrine_cache.providers.my_redis_cache', 'connect'],
            'persistent' => ['doctrine_cache.providers.my_persistent_redis_cache', 'pconnect'],
        ];
    }
}
