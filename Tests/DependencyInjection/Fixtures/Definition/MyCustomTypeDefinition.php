<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
