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

        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['providers'] as $name => $config) {
            $type = $config['type'];
            $id   = 'doctrine_cache.abstract.' . $type;

            if ( ! $container->hasDefinition($id)) {
                throw new \InvalidArgumentException(sprintf('"%s" is an unrecognized Doctrine cache driver.', $type));
            }

            $serviceId = 'doctrine_cache.providers.' . $name;
            $service   = $container->setDefinition($serviceId, new DefinitionDecorator($id));

            if ($config['namespace']) {
                $service->addMethodCall('setNamespace', array($config['namespace']));
            }

            if ($config['aliases']) {
                foreach ($config['aliases'] as $alias) {
                    $container->setAlias($alias, $serviceId);
                }
            }

            if ($this->hasDefinitionClass($type)) {
                $this->createCacheDefinition($type)->configure($name, $config, $service, $container);
            }
        }
    }

    /**
     * @param string $type
     *
     * @return \Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Definition\CacheDefinition
     */
    protected function createCacheDefinition($type)
    {
        $class  = $this->getDefinitionClass($type);
        $object = new $class($type);

        return $object;
    }

    /**
     * @param string $type
     *
     * @return boolean
     */
    protected function hasDefinitionClass($type)
    {
        return class_exists($this->getDefinitionClass($type));
    }

    /**
     * @param string $type
     *
     * @return string
     */
    protected function getDefinitionClass($type)
    {
        $name  = Inflector::classify($type) . 'Definition';
        $class = sprintf('%s\Definition\%s', __NAMESPACE__, $name);

        return $class;
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
