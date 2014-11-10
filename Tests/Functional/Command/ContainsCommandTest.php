<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Bundle\DoctrineCacheBundle\Command\ContainsCommand;

/**
 * Functional test for delete command.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class ContainsCommandTest extends CommandTestCase
{
    /**
     * @var \Doctrine\Bundle\DoctrineCacheBundle\Command\ContainsCommand
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

        $this->command = new ContainsCommand();
        $this->tester  = $this->getTester($this->command);
    }

    /**
     * Tests if a cache does not contain an entry.
     */
    public function testContainsFalse()
    {
        $this->tester->execute(array(
            'cache-name' => $this->cacheName,
            'cache-id'   => $this->cacheId,
        ));
        $this->assertEquals("FALSE\n", $this->tester->getDisplay());
    }

    /**
     * Tests if a cache contains an entry.
     */
    public function testContainsTrue()
    {
        $this->provider->save($this->cacheId, 'hello world');
        $this->tester->execute(array(
            'cache-name' => $this->cacheName,
            'cache-id' => $this->cacheId,
        ));
        $this->assertEquals("TRUE\n", $this->tester->getDisplay());
    }
}
