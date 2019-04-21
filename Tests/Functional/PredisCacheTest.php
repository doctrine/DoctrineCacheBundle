<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use function class_exists;

/**
 * Predis Driver Test
 *
 * @group Functional
 * @group Predis
 */
class PredisCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        if (class_exists('Doctrine\Common\Cache\PredisCache')) {
            return;
        }

        $this->markTestSkipped('The ' . self::class . ' requires the use of PredisCache available in doctrine/cache since 1.4');
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('predis');

        return $container->get('doctrine_cache.providers.my_predis_cache');
    }
}
