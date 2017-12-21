<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

/**
 * @group Functional
 * @group MongoDB
 */
class MongoDBCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        if ( ! extension_loaded('mongo')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of mongo >= 1.3.0');
        }

        if (@fsockopen('localhost', 27017) === false) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to mongo');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('mongodb');
        $cache     = $container->get('doctrine_cache.providers.my_mongodb_cache');

        return $cache;
    }
}
