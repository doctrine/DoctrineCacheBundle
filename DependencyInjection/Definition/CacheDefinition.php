<?php

declare(strict_types=1);

namespace Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Cache Definition.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class CacheDefinition
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @param string                                                    $name
     * @param array                                                     $config
     * @param \Symfony\Component\DependencyInjection\Definition         $service
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     */
    abstract public function configure($name, array $config, Definition $service, ContainerBuilder $container);
}
