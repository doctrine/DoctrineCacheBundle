<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

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

        if ( ! extension_loaded('riak')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of riak');
        }

        if (@fsockopen('localhost', 8087) === false) {
            $this->markTestSkipped('The ' . __CLASS__ .' cannot connect to riak');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function createCacheDriver()
    {
        $container = $this->compileContainer('riak');
        $cache     = $container->get('doctrine_cache.providers.my_riak_cache');

        return $cache;
    }
}
