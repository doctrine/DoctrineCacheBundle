<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

/**
 * @group Functional
 * @group Array
 */
class ArrayCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('array');
        $cache     = $container->get('doctrine_cache.providers.my_array_cache');

        return $cache;
    }
}
