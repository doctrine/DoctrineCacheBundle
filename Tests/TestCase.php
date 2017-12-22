<?php
namespace Doctrine\Bundle\DoctrineCacheBundle\Tests;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $bundles
     * @param string $vendor
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected function createContainer(array $bundles = array('YamlBundle'), $vendor = null)
    {
        $mappings = array();

        foreach ($bundles as $bundle) {
            require_once __DIR__.'/DependencyInjection/Fixtures/Bundles/'.($vendor ? $vendor.'/' : '').$bundle.'/'.$bundle.'.php';
            $mappings[$bundle] = 'DependencyInjection\\Fixtures\\Bundles\\'.($vendor ? $vendor.'\\' : '').$bundle.'\\'.$bundle;
        }

        return new ContainerBuilder(new ParameterBag(array(
            'kernel.debug'       => false,
            'kernel.bundles'     => $mappings,
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__.'/../',
            'kernel.name'        => 'test',
            'kernel.cache_dir'   => sys_get_temp_dir(),
        )));
    }
    
    /**
     * @param array $bundles
     * @param string $vendor
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected function createServiceContainer(array $bundles = array('YamlBundle'), $vendor = null)
    {
        $container = $this->createContainer($bundles, $vendor);
        $loader    = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        return $container;
    }
}
