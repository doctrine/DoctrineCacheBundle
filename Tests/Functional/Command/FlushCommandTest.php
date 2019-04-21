<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Doctrine\Bundle\DoctrineCacheBundle\Command\FlushCommand;
use Symfony\Component\Console\Tester\CommandTester;
use function sprintf;

/**
 * Functional test for delete command.
 */
class FlushCommandTest extends CommandTestCase
{
    /** @var FlushCommand */
    protected $command;

    /** @var CommandTester */
    protected $tester;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->command = new FlushCommand();
        $this->tester  = $this->getTester($this->command);
    }

    /**
     * Tests flushing a cache.
     */
    public function testFlush()
    {
        $this->tester->execute([
            'cache-name' => $this->cacheName,
        ]);
        $this->assertEquals(sprintf("Clearing the cache for the %s provider of type %s\n", $this->cacheName, $this->cacheProviderClass), $this->tester->getDisplay());
    }
}
