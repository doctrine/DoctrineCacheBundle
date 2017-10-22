<?php

declare(strict_types=1);

namespace Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * FileSystem definition.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FileSystemDefinition extends CacheDefinition
{
    /**
     * {@inheritDoc}
     */
    public function configure($name, array $config, Definition $service, ContainerBuilder $container)
    {
        $service->setArguments(array(
            $config['file_system']['directory'],
            $config['file_system']['extension'],
            $config['file_system']['umask']
        ));
    }
}
