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
 * Couchbase definition.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class CouchbaseDefinition extends CacheDefinition
{
    /**
     * {@inheritDoc}
     */
    public function configure($name, array $config, Definition $service, ContainerBuilder $container)
    {
        $host       = $config['couchbase']['hostnames'];
        $user       = $config['couchbase']['username'];
        $pass       = $config['couchbase']['password'];
        $bucket     = $config['couchbase']['bucket_name'];
        $connClass  = '%doctrine_cache.couchbase.connection.class%';
        $connId     = sprintf('doctrine_cache.services.%s_couchbase.connection', $name);
        $connDef    = new Definition($connClass, array($host, $user, $pass, $bucket));

        $connDef->setPublic(false);
        $container->setDefinition($connId, $connDef);
        $service->addMethodCall('setCouchbase', array($connDef));
    }
}
