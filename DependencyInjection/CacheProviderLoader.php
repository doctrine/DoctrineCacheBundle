<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Definition\CacheDefinition;
use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use function class_exists;
use function sprintf;

/**
 * Cache provider loader
 */
class CacheProviderLoader
{
    /**
     * @param string $name
     * @param array  $config
     */
    public function loadCacheProvider($name, array $config, ContainerBuilder $container)
    {
        $serviceId = 'doctrine_cache.providers.' . $name;
        $decorator = $this->getProviderDecorator($container, $config);
        $service   = $container->setDefinition($serviceId, $decorator);
        $type      = $config['type'] === 'custom_provider'
            ? $config['custom_provider']['type']
            : $config['type'];

        if ($config['namespace']) {
            $service->addMethodCall('setNamespace', [$config['namespace']]);
        }
        $service->setPublic(true);

        foreach ($config['aliases'] as $alias) {
            $container->setAlias($alias, new Alias($serviceId, true));
        }

        if (! $this->definitionClassExists($type, $container)) {
            return;
        }

        $this->getCacheDefinition($type, $container)->configure($name, $config, $service, $container);
    }

    /**
     * @param array $config
     *
     * @return DefinitionDecorator
     */
    protected function getProviderDecorator(ContainerBuilder $container, array $config)
    {
        $type = $config['type'];
        $id   = 'doctrine_cache.abstract.' . $type;

        static $childDefinition;

        if ($childDefinition === null) {
            $childDefinition = class_exists('Symfony\Component\DependencyInjection\ChildDefinition') ? 'Symfony\Component\DependencyInjection\ChildDefinition' : 'Symfony\Component\DependencyInjection\DefinitionDecorator';
        }

        if ($type === 'custom_provider') {
            $type  = $config['custom_provider']['type'];
            $param = $this->getCustomProviderParameter($type);

            if ($container->hasParameter($param)) {
                return new $childDefinition($container->getParameter($param));
            }
        }

        if ($container->hasDefinition($id)) {
            return new $childDefinition($id);
        }

        throw new InvalidArgumentException(sprintf('"%s" is an unrecognized Doctrine cache driver.', $type));
    }

    /**
     * @param string $type
     *
     * @return CacheDefinition
     */
    private function getCacheDefinition($type, ContainerBuilder $container)
    {
        $class = $this->getDefinitionClass($type, $container);

        return new $class($type);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function definitionClassExists($type, ContainerBuilder $container)
    {
        if ($container->hasParameter($this->getCustomDefinitionClassParameter($type))) {
            return true;
        }

        return class_exists($this->getDefinitionClass($type, $container));
    }

    /**
     * @param string $type
     *
     * @return string
     */
    protected function getDefinitionClass($type, ContainerBuilder $container)
    {
        if ($container->hasParameter($this->getCustomDefinitionClassParameter($type))) {
            return $container->getParameter($this->getCustomDefinitionClassParameter($type));
        }

        $name = Inflector::classify($type) . 'Definition';

        return sprintf('%s\Definition\%s', __NAMESPACE__, $name);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getCustomProviderParameter($type)
    {
        return 'doctrine_cache.custom_provider.' . $type;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getCustomDefinitionClassParameter($type)
    {
        return 'doctrine_cache.custom_definition_class.' . $type;
    }
}
