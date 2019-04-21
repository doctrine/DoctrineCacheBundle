<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use function extension_loaded;
use function fsockopen;

/**
 * @group Functional
 * @group Riak
 */
class RiakCacheTest extends BaseCacheTest
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        if (! extension_loaded('riak')) {
            $this->markTestSkipped('The ' . self::class . ' requires the use of riak');
        }

        if (@fsockopen('localhost', 8087) !== false) {
            return;
        }

        $this->markTestSkipped('The ' . self::class . ' cannot connect to riak');
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('riak');

        return $container->get('doctrine_cache.providers.my_riak_cache');
    }
}
