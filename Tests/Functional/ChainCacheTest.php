<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

/**
 * @group Functional
 * @group Chain
 */
class ChainCacheTest extends BaseCacheTest
{
    public function setUp()
    {
        parent::setUp();

        if (!class_exists('Doctrine\Common\Cache\ChainCache')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of ChainCache available in doctrine/cache since 1.4');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('chain');
        $cache     = $container->get('doctrine_cache.providers.my_chain_cache');

        return $cache;
    }
}
