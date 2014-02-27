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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Cache Bundle Extension
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DoctrineCacheExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader        = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $configuration = new Configuration();

        $loader->load('services.xml');

        $rootConfig = $this->processConfiguration($configuration, $configs);

        $this->loadAcl($rootConfig, $container);
        $this->loadCustomProviders($rootConfig, $container);
        $this->loadCacheProviders($rootConfig, $container);
    }

    /**
     * @param array                                                     $rootConfig
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     */
    protected function loadAcl(array $rootConfig, ContainerBuilder $container)
    {
        if ( ! isset($rootConfig['acl_cache']['id'])) {
            return;
        }

        $container->setParameter('doctrine_cache.acl_cache.id', $rootConfig['acl_cache']['id']);
        $container->setAlias('security.acl.cache', 'doctrine_cache.security.acl.cache');
    }

    /**
     * @param array                                                     $rootConfig
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     */
    protected function loadCacheProviders(array $rootConfig, ContainerBuilder $container)
    {
        foreach ($rootConfig['providers'] as $name => $config) {
            $this->loadCacheProvider($name, $config, $container);
        }

        foreach ($rootConfig['aliases'] as $alias => $name) {
            $container->setAlias($alias, 'doctrine_cache.providers.' . $name);
        }
    }

    /**
     * @param string                                                    $name
     * @param array                                                     $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     */
    protected function loadCacheProvider($name, array $config, ContainerBuilder $container)
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
    }

    /**
     * @param array                                                     $rootConfig
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     */
    protected function loadCustomProviders(array $rootConfig, ContainerBuilder $container)
    {
        foreach ($rootConfig['custom_providers'] as $type => $rootConfig) {
            $container->setParameter($this->getCustomProviderParameter($type), $rootConfig['prototype']);

            if ($rootConfig['definition_class']) {
                $container->setParameter($this->getCustomDefinitionClassParameter($type), $rootConfig['definition_class']);
            }
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
    private function getCustomProviderParameter($type)
    {
        return 'doctrine_cache.custom_provider.' . $type;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getCustomDefinitionClassParameter($type)
    {
        return 'doctrine_cache.custom_definition_class.' . $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'doctrine_cache';
    }

    /**
     * {@inheritDoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    /**
     * {@inheritDoc}
     **/
    public function getNamespace()
    {
        return 'http://doctrine-project.org/schemas';
    }
}
