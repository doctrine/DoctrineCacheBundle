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

        return $container->get('doctrine_cache.providers.my_filesystem_cache');
    }
}
