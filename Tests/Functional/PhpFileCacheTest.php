<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

/**
 * @group Functional
 * @group PhpFile
 */
class PhpFileCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('php_file');

        return $container->get('doctrine_cache.providers.my_phpfile_cache');
    }
}
