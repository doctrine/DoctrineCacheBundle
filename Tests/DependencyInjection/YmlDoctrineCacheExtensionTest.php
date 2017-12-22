<?php
namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * @group Extension
 * @group DependencyInjection
 */
class YmlDoctrineCacheExtensionTest extends AbstractDoctrineCacheExtensionTest
{
    /**
     * {@inheritdoc}
     */
    protected function loadFromFile(ContainerBuilder $container, $file)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/config/yml'));

        $loader->load($file . '.yml');
    }
}
