<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add logger proxy.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class CacheLoggerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Only add cache logging when in debug mode.
        $debug = $container->getParameter('kernel.debug');
        $logMasterId = 'doctrine_cache.log_master';
        if ( ! $debug || ! $container->hasDefinition($logMasterId)) {
            return;
        }

        // Find all cache providers.
        $taggedServices = $container->findTaggedServiceIds('doctrine_cache.provider');
        foreach ($taggedServices as $id => $tags) {
            // Get the cache provider service definition.
            $definition = $container->getDefinition($id);

            // Replace the provider class with the cache logger proxy.
            $providerClass = $container->getDefinition($definition->getParent())->getClass();
            $providerArgs  = $definition->getArguments();
            $definition->setClass('%doctrine_cache.cache_logger_proxy.class%');
            $definition->setArguments(array($id, $providerClass, $providerArgs));
            $definition->addMethodCall('setLogMaster', array(new Reference($logMasterId)));
        }
    }
}
