<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * AclCache Compiler Pass
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 *
 * @package Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Compiler
 */
class AclCacheCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ( ! $container->hasParameter('doctrine_cache.acl_cache.id')) {
            return;
        }

        $definition  = $container->getDefinition('doctrine_cache.security.acl.cache');
        $referenceId = $container->getParameter('doctrine_cache.acl_cache.id');
        $reference   = new Reference($referenceId);

        $definition->replaceArgument(0, $reference);
    }
}