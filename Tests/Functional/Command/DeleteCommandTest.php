<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Bundle\DoctrineCacheBundle\Command\DeleteCommand;

/**
 * Functional test for delete command.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class DeleteCommandTest extends CommandTestCase
{
    /**
     * @var \Doctrine\Bundle\DoctrineCacheBundle\Command\DeleteCommand
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

        $this->command = new DeleteCommand();
        $this->tester  = $this->getTester($this->command);
    }

    /**
     * Tests a cache delete success.
     */
    public function testDeleteSuccess()
    {
        $this->provider->save($this->cacheId, 'hello world');
        $this->tester->execute(array(
            'cache-name' => $this->cacheName,
            'cache-id' => $this->cacheId,
        ));
        $this->assertEquals("Deletion of {$this->cacheId} in {$this->cacheName} has succeeded\n", $this->tester->getDisplay());
    }

    /**
     * Tests a cache delete all.
     */
    public function testDeleteAll()
    {
        $this->tester->execute(array(
            'cache-name' => $this->cacheName,
            'cache-id'   => $this->cacheId,
            '--all'      => true
        ));
        $this->assertEquals("Deletion of all entries in {$this->cacheName} has succeeded\n", $this->tester->getDisplay());
    }
}
