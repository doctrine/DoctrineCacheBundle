<?php
namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * @group Extension
 * @group DependencyInjection
 */
class XmlDoctrineCacheExtensionTest extends AbstractDoctrineCacheExtensionTest
{
    /**
     * {@inheritdoc}
     */
    protected function loadFromFile(ContainerBuilder $container, $file)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/config/xml'));

        $loader->load($file . '.xml');
    }
}
