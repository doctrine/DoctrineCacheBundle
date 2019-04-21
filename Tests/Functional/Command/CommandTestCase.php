<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Doctrine\Bundle\DoctrineCacheBundle\Command\CacheCommand;
use Doctrine\Bundle\DoctrineCacheBundle\Tests\FunctionalTestCase;
use Doctrine\Common\Cache\Cache;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\Kernel;

class CommandTestCase extends FunctionalTestCase
{
    /** @var string */
    protected $cacheProviderClass = 'Doctrine\Common\Cache\ArrayCache';

    /** @var string */
    protected $cacheName = 'my_array_cache';

    /** @var string */
    protected $cacheId = 'test_cache_id';

    /** @var */
    protected $container;

    /** @var Cache */
    protected $provider;

    /** @var Kernel */
    protected $kernel;

    /** @var Application */
    protected $app;

    public function setUp()
    {
        $this->container = $this->compileContainer('array');
        $this->provider  = $this->container->get('doctrine_cache.providers.' . $this->cacheName);
        $this->kernel    = $this->getMockKernel();
        $this->app       = new Application($this->kernel);
    }

    /**
     * @return CommandTester
     */
    protected function getTester(CacheCommand $command)
    {
        $command->setContainer($this->container);
        $command->setApplication($this->app);

        return new CommandTester($command);
    }

    /**
     * Gets Kernel mock instance
     *
     * @return Kernel
     */
    private function getMockKernel()
    {
        $mock = $this->getMockBuilder('\Symfony\Component\HttpKernel\Kernel')
                     ->disableOriginalConstructor()
                     ->disableOriginalClone()
                     ->getMock();
        $mock->method('getBundles')->willReturn([]);
        $mock->method('getContainer')->willReturn($this->container);

        return $mock;
    }
}
