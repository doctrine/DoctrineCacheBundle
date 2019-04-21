<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Doctrine\Bundle\DoctrineCacheBundle\Command\DeleteCommand;
use Symfony\Component\Console\Tester\CommandTester;
use function sprintf;

/**
 * Functional test for delete command.
 */
class DeleteCommandTest extends CommandTestCase
{
    /** @var DeleteCommand */
    protected $command;

    /** @var CommandTester */
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
        $this->tester->execute([
            'cache-name' => $this->cacheName,
            'cache-id' => $this->cacheId,
        ]);
        $this->assertEquals(sprintf("Deletion of %s in %s has succeeded\n", $this->cacheId, $this->cacheName), $this->tester->getDisplay());
    }

    /**
     * Tests a cache delete all.
     */
    public function testDeleteAll()
    {
        $this->tester->execute([
            'cache-name' => $this->cacheName,
            'cache-id'   => $this->cacheId,
            '--all'      => true,
        ]);
        $this->assertEquals(sprintf("Deletion of all entries in %s has succeeded\n", $this->cacheName), $this->tester->getDisplay());
    }
}
