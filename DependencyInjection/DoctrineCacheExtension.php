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

use Symfony\Component\Config\FileLocator;
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
     * @var \Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\CacheProviderLoader
     */
    private $loader;

    /**
     * @param \Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\CacheProviderLoader $loader
     */
    public function __construct(CacheProviderLoader $loader = null)
    {
        $this->loader = $loader ?: new CacheProviderLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config/');
        $loader  = new XmlFileLoader($container, $locator);

        $loader->load('services.xml');

        $configuration = new Configuration();
        $rootConfig    = $this->processConfiguration($configuration, $configs);

        $this->loadAcl($rootConfig, $container);
        $this->loadCustomProviders($rootConfig, $container);
        $this->loadCacheProviders($rootConfig, $container);
        $this->loadCacheAliases($rootConfig, $container);
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
            $this->loader->loadCacheProvider($name, $config, $container);
        }
    }

    /**
     * @param array                                                     $rootConfig
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     */
    protected function loadCacheAliases(array $rootConfig, ContainerBuilder $container)
    {
        foreach ($rootConfig['aliases'] as $alias => $name) {
            $container->setAlias($alias, 'doctrine_cache.providers.' . $name);
        }
    }

    /**
     * @param array                                                     $rootConfig
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder   $container
     */
    protected function loadCustomProviders(array $rootConfig, ContainerBuilder $container)
    {
        foreach ($rootConfig['custom_providers'] as $type => $rootConfig) {
            $providerParameterName   = $this->loader->getCustomProviderParameter($type);
            $definitionParameterName = $this->loader->getCustomDefinitionClassParameter($type);

            $container->setParameter($providerParameterName, $rootConfig['prototype']);

            if ($rootConfig['definition_class']) {
                $container->setParameter($definitionParameterName, $rootConfig['definition_class']);
            }
        }
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
