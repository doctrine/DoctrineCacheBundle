<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Command;

use Doctrine\Bundle\DoctrineCacheBundle\Tests\FunctionalTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Common\Cache\Cache;
use Doctrine\Bundle\DoctrineCacheBundle\Command\CacheCommand;

class CommandTestCase extends FunctionalTestCase
{
    /**
     * @var string
     */
    protected $cacheProviderClass = 'Doctrine\Common\Cache\ArrayCache';

    /**
     * @var string
     */
    protected $cacheName = 'my_array_cache';

    /**
     * @var string
     */
    protected $cacheId   = 'test_cache_id';

    /**
     * @var
     */
    protected $container;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $provider;

    /**
     * @var \Symfony\Component\HttpKernel\Kernel
     */
    protected $kernel;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Console\Application
     */
    protected $app;

    public function setUp()
    {
        $this->container = $this->compileContainer('array');
        $this->provider  = $this->container->get('doctrine_cache.providers.' . $this->cacheName);
        $this->kernel    = $this->getMockKernel();
        $this->app       = new Application($this->kernel);
    }

    /**
     * @param \Doctrine\Bundle\DoctrineCacheBundle\Command\CacheCommand $command
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
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
     * @return \Symfony\Component\HttpKernel\Kernel
     */
    private function getMockKernel()
    {
        $mock = $this->getMock('\Symfony\Component\HttpKernel\Kernel', array(), array(), '', false, false);
        $mock->method('getBundles')->willReturn(array());
        $mock->method('getContainer')->willReturn($this->container);
        return $mock;
    }

    /**
     * Gets Filesystem mock instance
     *
     * @return \Symfony\Bundle\FrameworkBundle\Util\Filesystem
     */
    private function getMockFilesystem()
    {
        return $this->getMock('\Symfony\Bundle\FrameworkBundle\Util\Filesystem', array(), array(), '', false, false);
    }
}
