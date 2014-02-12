<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * MongoDB definition.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MongodbDefinition extends CacheDefinition
{
    /**
     * {@inheritDoc}
     */
    public function configure($name, array $config, Definition $service, ContainerBuilder $container)
    {
        $server         = $config['mongodb']['server'];
        $databaseName   = $config['mongodb']['database_name'];
        $collectionName = $config['mongodb']['collection_name'];
        $connClass      = '%doctrine_cache.mongodb.connection.class%';
        $collClass      = '%doctrine_cache.mongodb.collection.class%';
        $connId         = sprintf('doctrine_cache.services.%s.connection', $name);
        $collId         = sprintf('doctrine_cache.services.%s.collection', $name);
        $collDef        = new Definition($collClass, array($databaseName, $collectionName));
        $connDef        = new Definition($connClass, array($server));

        $connDef->addMethodCall('connect');
        $container->setDefinition($connId, $connDef);

        $container->setDefinition($collId, $collDef)
            ->setFactoryMethod('selectCollection')
            ->setFactoryService($connId)
            ->setPublic(false);

        $service->setArguments(array($collDef));
    }
}
