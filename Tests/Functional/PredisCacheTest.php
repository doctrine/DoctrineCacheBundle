<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

/**
 * Predis Driver Test
 *
 * @group Functional
 * @group Predis
 * @author Ivo Bathke <ivo.bathke@gmail.com>
 */
class PredisCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        if ( ! class_exists('Doctrine\Common\Cache\PredisCache')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of PredisCache available in doctrine/cache since 1.4');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('predis');
        $cache     = $container->get('doctrine_cache.providers.my_predis_cache');

        return $cache;
    }
}
