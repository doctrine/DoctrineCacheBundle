<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use function hash;
use function in_array;

/**
 * Symfony bridge adpter
 */
class SymfonyBridgeAdapter
{
    /** @var CacheProviderLoader */
    private $cacheProviderLoader;

    /** @var string */
    protected $objectManagerName;

    /** @var string */
    protected $mappingResourceName;

    /**
     * @param string $objectManagerName
     * @param string $mappingResourceName
     */
    public function __construct(CacheProviderLoader $cacheProviderLoader, $objectManagerName, $mappingResourceName)
    {
        $this->cacheProviderLoader = $cacheProviderLoader;
        $this->objectManagerName   = $objectManagerName;
        $this->mappingResourceName = $mappingResourceName;
    }

    public function loadServicesConfiguration(ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config/');
        $loader  = new XmlFileLoader($container, $locator);

        $loader->load('services.xml');
    }

    /**
     * @param string $cacheName
     * @param string $objectManagerName
     * @param array  $cacheDriver
     *
     * @return string
     */
    public function loadCacheDriver($cacheName, $objectManagerName, array $cacheDriver, ContainerBuilder $container)
    {
        $id       = $this->getObjectManagerElementName($objectManagerName . '_' . $cacheName);
        $host     = $cacheDriver['host'] ?? null;
        $port     = $cacheDriver['port'] ?? null;
        $password = $cacheDriver['password'] ?? null;
        $database = $cacheDriver['database'] ?? null;
        $type     = $cacheDriver['type'];

        if ($type === 'service') {
            $container->setAlias($id, new Alias($cacheDriver['id'], false));

            return $id;
        }

        $config = [
            'aliases'   => [$id],
            $type       => [],
            'type'      => $type,
            'namespace' => null,
        ];

        if (! isset($cacheDriver['namespace'])) {
            // generate a unique namespace for the given application
            $seed = '_' . $container->getParameter('kernel.root_dir');

            if ($container->hasParameter('cache.prefix.seed')) {
                $seed = '.' . $container->getParameterBag()->resolveValue($container->getParameter('cache.prefix.seed'));
            }

            $seed     .= '.' . $container->getParameter('kernel.name') . '.' . $container->getParameter('kernel.environment');
            $hash      = hash('sha256', $seed);
            $namespace = 'sf_' . $this->mappingResourceName . '_' . $objectManagerName . '_' . $hash;

            $cacheDriver['namespace'] = $namespace;
        }

        $config['namespace'] = $cacheDriver['namespace'];

        if (in_array($type, ['memcache', 'memcached'])) {
            $host                            = ! empty($host) ? $host : 'localhost';
            $config[$type]['servers'][$host] = [
                'host' => $host,
                'port' => ! empty($port) ? $port : 11211,
            ];
        }

        if ($type === 'redis') {
            $config[$type] = [
                'host' => ! empty($host) ? $host : 'localhost',
                'port' => ! empty($port) ? $port : 6379,
                'password' => ! empty($password) ? $password : null,
                'database' => ! empty($database) ? $database : 0,
            ];
        }

        if ($type === 'predis') {
            $config[$type] = [
                'scheme' => 'tcp',
                'host' => ! empty($host) ? $host : 'localhost',
                'port' => ! empty($port) ? $port : 6379,
                'password' => ! empty($password) ? $password : null,
                'database' => ! empty($database) ? $database : 0,
                'timeout' => null,
            ];
        }

        $this->cacheProviderLoader->loadCacheProvider($id, $config, $container);

        return $id;
    }

    /**
     * @param array  $objectManager
     * @param string $cacheName
     */
    public function loadObjectManagerCacheDriver(array $objectManager, ContainerBuilder $container, $cacheName)
    {
        $this->loadCacheDriver($cacheName, $objectManager['name'], $objectManager[$cacheName . '_driver'], $container);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getObjectManagerElementName($name)
    {
        return $this->objectManagerName . '.' . $name;
    }
}
