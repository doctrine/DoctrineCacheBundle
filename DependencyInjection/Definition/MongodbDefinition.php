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
use Symfony\Component\DependencyInjection\Reference;

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
        $memcacheConf = $config['mongodb'];
        $collRef      = $this->getCollectionReference($name, $memcacheConf, $container);

        $service->setArguments(array($collRef));
    }

    /**
     * @param string                                                    $name
     * @param array                                                     $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     *
     * @return \Symfony\Component\DependencyInjection\Reference
     */
    private function getCollectionReference($name, array $config, ContainerBuilder $container)
    {
        if (isset($config['collection_id'])) {
            return new Reference($config['collection_id']);
        }

        $connRef = $this->getConnectionReference($name, $config, $container);

        $doctrineCollFactoryClass = '%doctrine_cache.mongodb.collection.class%';
        $doctrineCollFactoryId = sprintf('doctrine_cache.services.%s.collection.factory', $name);
        $doctrineCollFactoryDef = new Definition($doctrineCollFactoryClass, array(
            $config['database_name'],
            $config['collection_name']
        ));
        $doctrineCollFactoryDef
            ->setFactory(array($connRef, 'selectCollection'))
            ->setPublic(false)
        ;
        $container->setDefinition($doctrineCollFactoryId, $doctrineCollFactoryDef);

        $collClass = '%doctrine_cache.mongodb.collection.class%';
        $collId = sprintf('doctrine_cache.services.%s.collection', $name);
        $collDef = new Definition($collClass);
        $collDef
            ->setFactory(array(new Reference($doctrineCollFactoryId), 'getMongoCollection'))
            ->setPublic(false)
        ;
        $container->setDefinition($collId, $collDef);

        return new Reference($collId);
    }

    /**
     * @param string                                                    $name
     * @param array                                                     $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     *
     * @return \Symfony\Component\DependencyInjection\Reference
     */
    private function getConnectionReference($name, array $config, ContainerBuilder $container)
    {
        if (isset($config['connection_id'])) {
            return new Reference($config['connection_id']);
        }

        $server         = $config['server'];
        $connClass      = '%doctrine_cache.mongodb.connection.class%';
        $connId         = sprintf('doctrine_cache.services.%s.connection', $name);
        $connDef        = new Definition($connClass, array($server));

        $connDef->setPublic(false);
        $connDef->addMethodCall('connect');

        $container->setDefinition($connId, $connDef);

        return new Reference($connId);
    }
}
