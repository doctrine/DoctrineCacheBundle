<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

/**
 * @group Functional
 * @group FileSystem
 */
class FileSystemCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('file_system');
        $cache     = $container->get('doctrine_cache.providers.my_filesystem_cache');

        return $cache;
    }
}
