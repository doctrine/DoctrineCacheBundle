<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Doctrine\Bundle\DoctrineCacheBundle\Command\ContainsCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Functional test for delete command.
 */
class ContainsCommandTest extends CommandTestCase
{
    /** @var ContainsCommand */
    protected $command;

    /** @var CommandTester */
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
        $this->tester->execute([
            'cache-name' => $this->cacheName,
            'cache-id'   => $this->cacheId,
        ]);
        $this->assertEquals("FALSE\n", $this->tester->getDisplay());
    }

    /**
     * Tests if a cache contains an entry.
     */
    public function testContainsTrue()
    {
        $this->provider->save($this->cacheId, 'hello world');
        $this->tester->execute([
            'cache-name' => $this->cacheName,
            'cache-id' => $this->cacheId,
        ]);
        $this->assertEquals("TRUE\n", $this->tester->getDisplay());
    }
}
