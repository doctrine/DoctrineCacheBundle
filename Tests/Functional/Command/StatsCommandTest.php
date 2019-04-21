<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Doctrine\Bundle\DoctrineCacheBundle\Command\StatsCommand;
use Symfony\Component\Console\Tester\CommandTester;
use function strpos;

/**
 * Functional test for delete command.
 */
class StatsCommandTest extends CommandTestCase
{
    /** @var StatsCommand */
    protected $command;

    /** @var CommandTester */
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
        $this->tester->execute([
            'cache-name' => $this->cacheName,
        ]);

        $stats = $this->tester->getDisplay();

        if (strpos($stats, 'Stats were not') === false) {
            // This test is for Doctrine/Cache >= 1.6.0 only
            $this->assertStringStartsWith(
                "Stats for the {$this->cacheName} provider of type Doctrine\\Common\\Cache\\ArrayCache:",
                $stats
            );
            $this->assertContains("[hits] 0\n", $stats);
            $this->assertContains("[misses] 0\n", $stats);
            $this->assertRegExp('/\[uptime\] [0-9]{10}' . "\n/", $stats);
            $this->assertContains("[memory_usage] \n", $stats);
            $this->assertContains("[memory_available] \n", $stats);
        } else {
            // This test is for Doctrine/Cache < 1.6.0 only
            $this->assertEquals("Stats were not provided for the {$this->cacheName} provider of type Doctrine\\Common\\Cache\\ArrayCache\n", $this->tester->getDisplay());
        }
    }
}
