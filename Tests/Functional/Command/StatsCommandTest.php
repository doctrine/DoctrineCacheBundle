<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Bundle\DoctrineCacheBundle\Command\StatsCommand;

/**
 * Functional test for delete command.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class StatsCommandTest extends CommandTestCase
{
    /**
     * @var \Doctrine\Bundle\DoctrineCacheBundle\Command\StatsCommand
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

        $this->command = new StatsCommand();
        $this->tester  = $this->getTester($this->command);
    }

    /**
     * Tests getting cache provider stats.
     */
    public function testStats()
    {
        $this->tester->execute(array(
            'cache-name' => $this->cacheName,
        ));
        $this->assertEquals("Stats were not provided for the {$this->cacheName} provider of type Doctrine\\Common\\Cache\\ArrayCache\n", $this->tester->getDisplay());
    }
}
