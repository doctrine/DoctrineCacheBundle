<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Cache provider loader
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class CacheProviderLoader
{
    /**
     * @param string                                                    $name
     * @param array                                                     $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     */
    public function loadCacheProvider($name, array $config, ContainerBuilder $container)
    {
        $serviceId  = 'doctrine_cache.providers.' . $name;
        $decorator  = $this->getProviderDecorator($container, $config);
        $service    = $container->setDefinition($serviceId, $decorator);
        $type       = ($config['type'] === 'custom_provider')
            ? $config['custom_provider']['type']
            : $config['type'];

        if ($config['namespace']) {
            $service->addMethodCall('setNamespace', array($config['namespace']));
        }

        foreach ($config['aliases'] as $alias) {
            $container->setAlias($alias, $serviceId);
        }

        if ($this->definitionClassExists($type, $container)) {
            $this->getCacheDefinition($type, $container)->configure($name, $config, $service, $container);
        }

        if (1) {
            $loggerServiceId = $serviceId . '.logger';
            $logger = new Definition(
                $container->getParameter('doctrine_cache.cache_logger_proxy.class'),
                array(
                    new Reference($loggerServiceId . '.inner'),
                    $name,
                )
            );
            $logger->setPublic(false);
            $logger->setDecoratedService($serviceId);

            $logMasterId = 'doctrine_cache.log_master';
            $logger->addMethodCall('setLogMaster', array(new Reference($logMasterId)));

            $container->setDefinition($loggerServiceId, $logger);
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     * @param array                                                     $config
     *
     * @return \Symfony\Component\DependencyInjection\DefinitionDecorator
     */
    protected function getProviderDecorator(ContainerBuilder $container, array $config)
    {
        $type = $config['type'];
        $id   = 'doctrine_cache.abstract.' . $type;

        if ($type === 'custom_provider') {
            $type  = $config['custom_provider']['type'];
            $param = $this->getCustomProviderParameter($type);

            if ($container->hasParameter($param)) {
                return new DefinitionDecorator($container->getParameter($param));
            }
        }

        if ($container->hasDefinition($id)) {
            return new DefinitionDecorator($id);
        }

        throw new \InvalidArgumentException(sprintf('"%s" is an unrecognized Doctrine cache driver.', $type));
    }

    /**
     * @param string                                                    $type
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     *
     * @return \Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Definition\CacheDefinition
     */
    private function getCacheDefinition($type, ContainerBuilder $container)
    {
        $class  = $this->getDefinitionClass($type, $container);
        $object = new $class($type);

        return $object;
    }

    /**
     * @param string                                                    $type
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     *
     * @return boolean
     */
    private function definitionClassExists($type, ContainerBuilder $container)
    {
        if ($container->hasParameter($this->getCustomDefinitionClassParameter($type))) {
            return true;
        }

        return class_exists($this->getDefinitionClass($type, $container));
    }

    /**
     * @param string                                                    $type
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     *
     * @return string
     */
    protected function getDefinitionClass($type, ContainerBuilder $container)
    {
        if ($container->hasParameter($this->getCustomDefinitionClassParameter($type))) {
            return $container->getParameter($this->getCustomDefinitionClassParameter($type));
        }

        $name  = Inflector::classify($type) . 'Definition';
        $class = sprintf('%s\Definition\%s', __NAMESPACE__, $name);

        return $class;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getCustomProviderParameter($type)
    {
        return 'doctrine_cache.custom_provider.' . $type;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getCustomDefinitionClassParameter($type)
    {
        return 'doctrine_cache.custom_definition_class.' . $type;
    }
}
