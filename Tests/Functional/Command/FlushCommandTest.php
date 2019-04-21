<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Doctrine\Bundle\DoctrineCacheBundle\Command\FlushCommand;
use Symfony\Component\Console\Tester\CommandTester;

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
        $this->assertEquals("Clearing the cache for the {$this->cacheName} provider of type {$this->cacheProviderClass}\n", $this->tester->getDisplay());
    }
}
