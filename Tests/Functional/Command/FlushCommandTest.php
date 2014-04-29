<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Bundle\DoctrineCacheBundle\Command\FlushCommand;

/**
 * Functional test for delete command.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class FlushCommandTest extends CommandTestCase
{
    /**
     * @var \Doctrine\Bundle\DoctrineCacheBundle\Command\FlushCommand
     */
    protected $command;

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
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
        $this->tester->execute(array(
            'cache-name' => $this->cacheName,
        ));
        $this->assertEquals("Clearing the cache for the {$this->cacheName} provider of type {$this->cacheProviderClass}\n", $this->tester->getDisplay());
    }
}
