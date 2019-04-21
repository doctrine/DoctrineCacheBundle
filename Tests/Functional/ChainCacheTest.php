<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use function class_exists;

/**
 * @group Functional
 * @group Chain
 */
class ChainCacheTest extends BaseCacheTest
{
    public function setUp()
    {
        parent::setUp();

        if (class_exists('Doctrine\Common\Cache\ChainCache')) {
            return;
        }

        $this->markTestSkipped('The ' . self::class . ' requires the use of ChainCache available in doctrine/cache since 1.4');
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('chain');

        return $container->get('doctrine_cache.providers.my_chain_cache');
    }
}
