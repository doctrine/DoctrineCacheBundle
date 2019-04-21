<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests;

use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\DoctrineCacheExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class FunctionalTestCase extends TestCase
{
    /**
     * @param type $file
     */
    protected function loadFromFile(ContainerBuilder $container, $file)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Functional/Fixtures/config'));

        $loader->load($file . '.xml');
    }

    /**
     * @param string $file
     *
     * @return ContainerBuilder
     */
    protected function compileContainer($file, ?ContainerBuilder $container = null)
    {
        $container = $container ?: $this->createContainer();
        $loader    = new DoctrineCacheExtension();

        $container->registerExtension($loader);
        $this->loadFromFile($container, $file);
        $this->overrideContainer($container);
        $container->compile();

        return $container;
    }

    /**
     * Override this hook in your functional TestCase to customize the container
     */
    protected function overrideContainer(ContainerBuilder $container)
    {
    }
}
