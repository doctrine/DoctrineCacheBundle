<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use function extension_loaded;
use function fsockopen;

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

        if (! extension_loaded('mongo')) {
            $this->markTestSkipped('The ' . self::class . ' requires the use of mongo >= 1.3.0');
        }

        if (@fsockopen('localhost', 27017) !== false) {
            return;
        }

        $this->markTestSkipped('The ' . self::class . ' cannot connect to mongo');
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('mongodb');

        return $container->get('doctrine_cache.providers.my_mongodb_cache');
    }
}
