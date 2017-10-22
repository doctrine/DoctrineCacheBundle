<?php

declare(strict_types=1);

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\DependencyInjection\Fixtures\Definition;

use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Definition\CacheDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class MyCustomTypeDefinition extends CacheDefinition
{
    /**
     * {@inheritDoc}
     */
    public function configure($name, array $config, Definition $service, ContainerBuilder $container)
    {
        foreach ($config['custom_provider']['options'] as $name => $value) {
            $service->addMethodCall('addConfig', array($name, $value));
        }
    }
}
