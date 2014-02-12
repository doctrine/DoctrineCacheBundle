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
 * Riak definition.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakDefinition extends CacheDefinition
{
    /**
     * {@inheritDoc}
     */
    public function configure($name, array $config, Definition $service, ContainerBuilder $container)
    {
        $host           = $config['riak']['host'];
        $port           = $config['riak']['port'];
        $bucketName     = $config['riak']['bucket_name'];
        $bucketClass    = '%doctrine_cache.riak.bucket.class%';
        $connClass      = '%doctrine_cache.riak.connection.class%';
        $bucketId       = sprintf('doctrine_cache.services.%s.bucket', $name);
        $connId         = sprintf('doctrine_cache.services.%s.connection', $name);
        $connDef        = new Definition($connClass, array($host, $port));
        $bucketDef      = new Definition($bucketClass, array($connDef, $bucketName));

        $connDef->setPublic(false);
        $bucketDef->setPublic(false);

        $container->setDefinition($connId, $connDef);
        $container->setDefinition($bucketId, $bucketDef);

        if ( ! empty($config['riak']['bucket_property_list'])) {
            $this->configureBucketPropertyList($name, $config['riak']['bucket_property_list'], $bucketDef, $container);
        }

        $service->setArguments(array($bucketDef));
    }

    /**
     * @param string                                                    $name
     * @param array                                                     $config
     * @param \Symfony\Component\DependencyInjection\Definition         $bucketDefinition
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     */
    private function configureBucketPropertyList($name, array $config, Definition $bucketDefinition, ContainerBuilder $container)
    {
        $propertyListClass      = '%doctrine_cache.riak.bucket_property_list.class%';
        $propertyListServiceId  = sprintf('doctrine_cache.services.%s.bucket_property_list', $name);
        $propertyListReference  = new Reference($propertyListServiceId);
        $propertyListDefinition = new Definition($propertyListClass, array(
            $config['n_value'],
            $config['allow_multiple']
        ));

        $container->setDefinition($propertyListServiceId, $propertyListDefinition);
        $bucketDefinition->addMethodCall('setPropertyList', array($propertyListReference));
    }
}
