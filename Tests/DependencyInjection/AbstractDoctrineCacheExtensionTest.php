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

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\DependencyInjection;

use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\Compiler\AclCacheCompilerPass;
use Doctrine\Bundle\DoctrineCacheBundle\Tests\TestCase;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Doctrine\Bundle\DoctrineCacheBundle\DependencyInjection\DoctrineCacheExtension;

/**
 * @group Extension
 * @group DependencyInjection
 */
abstract class AbstractDoctrineCacheExtensionTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    abstract protected function loadFromFile(ContainerBuilder $container, $file);

    public function testBasicCache()
    {
        $container = $this->compileContainer('basic');
        $drivers   = array(
            'basic_apc_provider'         => 'Doctrine\Common\Cache\ApcCache',
            'basic_array_provider'       => 'Doctrine\Common\Cache\ArrayCache',
            'basic_xcache_provider'      => 'Doctrine\Common\Cache\XcacheCache',
            'basic_wincache_provider'    => 'Doctrine\Common\Cache\WinCacheCache',
            'basic_zenddata_provider'    => 'Doctrine\Common\Cache\ZendDataCache',
            'basic_ns_zenddata_provider' => 'Doctrine\Common\Cache\ZendDataCache',

            'basic_apc_provider2'         => 'Doctrine\Common\Cache\ApcCache',
            'basic_array_provider2'       => 'Doctrine\Common\Cache\ArrayCache',
            'basic_xcache_provider2'      => 'Doctrine\Common\Cache\XcacheCache',
            'basic_wincache_provider2'    => 'Doctrine\Common\Cache\WinCacheCache',
            'basic_zenddata_provider2'    => 'Doctrine\Common\Cache\ZendDataCache'
        );

        foreach ($drivers as $key => $value) {
            $this->assertCacheProvider($container, $key, $value);
        }
    }

    public function testBasicConfigurableCache()
    {
        $container = $this->compileContainer('configurable');
        $drivers   = array(
            'configurable_memcached_provider' => array(
                'Doctrine\Common\Cache\MemcachedCache', array('setMemcached' => array())
            ),
            'configurable_memcache_provider' => array(
                'Doctrine\Common\Cache\MemcacheCache', array('setMemcache' => array())
            ),
            'configurable_redis_provider' => array(
                'Doctrine\Common\Cache\RedisCache', array('setRedis' => array())
            ),
            'configurable_mongodb_provider' => array(
                'Doctrine\Common\Cache\MongoDBCache'
            ),
            'configurable_riak_provider' => array(
                'Doctrine\Common\Cache\RiakCache'
            ),
            'configurable_filesystem_provider' => array(
                'Doctrine\Common\Cache\FilesystemCache'
            ),
            'configurable_phpfile_provider' => array(
                'Doctrine\Common\Cache\PhpFileCache'
            ),
            'configurable_couchbase_provider' => array(
                'Doctrine\Common\Cache\CouchbaseCache'
            ),
        );

        foreach ($drivers as $id => $value) {
            $this->assertCacheProvider($container, $id, $value[0]);
        }
    }

    public function testBasicConfigurableDefaultCache()
    {
        $container = $this->compileContainer('configurable_defaults');
        $drivers   = array(
            'configurable_memcached_provider' => array(
                'Doctrine\Common\Cache\MemcachedCache', array('setMemcached' => array())
            ),
            'configurable_memcache_provider' => array(
                'Doctrine\Common\Cache\MemcacheCache', array('setMemcache' => array())
            ),
            'configurable_redis_provider' => array(
                'Doctrine\Common\Cache\RedisCache', array('setRedis' => array())
            ),
            'configurable_mongodb_provider' => array(
                'Doctrine\Common\Cache\MongoDBCache'
            ),
            'configurable_riak_provider' => array(
                'Doctrine\Common\Cache\RiakCache'
            ),
            'configurable_filesystem_provider' => array(
                'Doctrine\Common\Cache\FilesystemCache'
            ),
            'configurable_phpfile_provider' => array(
                'Doctrine\Common\Cache\PhpFileCache'
            ),
            'configurable_couchbase_provider' => array(
                'Doctrine\Common\Cache\CouchbaseCache'
            ),
        );

        foreach ($drivers as $id => $value) {
            $this->assertCacheProvider($container, $id, $value[0]);
        }
    }

    public function testBasicNamespaceCache()
    {
        $container = $this->compileContainer('namespaced');
        $drivers   = array(
            'doctrine_cache.providers.foo_namespace_provider' => 'foo_namespace',
            'doctrine_cache.providers.barNamespaceProvider'   => 'barNamespace',
        );

        foreach ($drivers as $key => $value) {
            $this->assertTrue($container->hasDefinition($key));

            $def   = $container->getDefinition($key);
            $calls = $def->getMethodCalls();

            $this->assertEquals('setNamespace', $calls[0][0]);
            $this->assertEquals($value, $calls[0][1][0]);
        }
    }

    public function testAliasesCache()
    {
        $container = $this->compileContainer('aliased');
        $providers = array(
            'doctrine_cache.providers.foo_namespace_provider' => array('fooNamespaceProvider', 'foo'),
            'doctrine_cache.providers.barNamespaceProvider'   => array('bar_namespace_provider', 'bar'),
        );

        foreach ($providers as $key => $aliases) {
            $this->assertTrue($container->hasDefinition($key));

            foreach ($aliases as $alias) {
                $this->assertEquals(strtolower($key), (string) $container->getAlias($alias));
            }
        }
    }

    public function testServiceParameters()
    {
        $container = $this->compileContainer('service_parameter');
        $providers = array(
            'service_bucket_riak_provider' => array(
                'Doctrine\Common\Cache\RiakCache'
            ),
            'service_connection_riak_provider' => array(
                'Doctrine\Common\Cache\RiakCache'
            ),
            'service_connection_memcached_provider' => array(
                'Doctrine\Common\Cache\MemcachedCache'
            ),
            'service_connection_memcache_provider' => array(
                'Doctrine\Common\Cache\MemcacheCache'
            ),
            'service_connection_redis_provider' => array(
                'Doctrine\Common\Cache\RedisCache'
            ),
            'service_connection_mongodb_provider' => array(
                'Doctrine\Common\Cache\MongoDBCache'
            ),
            'service_collection_mongodb_provider' => array(
                'Doctrine\Common\Cache\MongoDBCache'
            ),
        );

        foreach ($providers as $id => $value) {
            $this->assertCacheProvider($container, $id, $value[0]);
        }
    }

    public function testCustomCacheProviders()
    {
        $container = $this->compileContainer('custom_providers');
        $providers = array(
            'my_custom_type_provider' => array(
                'Doctrine\Bundle\DoctrineCacheBundle\Tests\DependencyInjection\Fixtures\Cache\MyCustomType',
                array('addConfig' => array(
                    array('config_foo', 'foo'),
                    array('config_bar', 'bar'),
                ))
            ),
            'my_custom_type_provider2' => array(
                'Doctrine\Bundle\DoctrineCacheBundle\Tests\DependencyInjection\Fixtures\Cache\MyCustomType',
                array()
            ),
        );

        foreach ($providers as $id => $value) {
            $this->assertCacheProvider($container, $id, $value[0], $value[1]);
        }
    }

     /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage "unrecognized_type" is an unrecognized Doctrine cache driver.
     */
    public function testUnrecognizedCacheDriverException()
    {
        $this->compileContainer('unrecognized');
    }

    public function testAcl()
    {
        $container = $this->compileContainer('acl');

        $this->assertEquals('acl_apc_provider', $container->getParameter('doctrine_cache.acl_cache.id'));
        $this->assertTrue($container->hasDefinition('doctrine_cache.security.acl.cache'));

        $definition = $container->getDefinition('doctrine_cache.security.acl.cache');

        $this->assertFalse($definition->isPublic());
    }

    public function assertCacheProvider(ContainerBuilder $container, $name, $class, array $expectedCalls = array())
    {
        $service = "doctrine_cache.providers." . $name;

        $this->assertTrue($container->hasDefinition($service));

        $definition = $container->getDefinition($service);

        $this->assertTrue($definition->isPublic());
        $this->assertEquals($class, $definition->getClass());

        foreach (array_unique($expectedCalls) as $methodName => $params) {
            $this->assertMethodCall($definition, $methodName, $params);
        }
    }

    public function assertCacheResource(ContainerBuilder $container, $name, $class, array $expectedCalls = array())
    {
        $service = "doctrine_cache.services.$name";

        $this->assertTrue($container->hasDefinition($service));

        $definition = $container->getDefinition($service);

        $this->assertTrue($definition->isPublic());
        $this->assertEquals($class, $definition->getClass());

        foreach ($expectedCalls as $methodName => $params) {
            $this->assertMethodCall($definition, $methodName, $params);
        }
    }

    private function assertMethodCall(Definition $definition, $methodName, array $parameters = array())
    {
        $methodCalls  = $definition->getMethodCalls();
        $actualCalls  = array();

        foreach ($methodCalls as $call) {
            $actualCalls[$call[0]][] = $call[1];
        }

        $this->assertArrayHasKey($methodName, $actualCalls);
        $this->assertCount(count($parameters), $actualCalls[$methodName]);

        foreach ($parameters as $index => $param) {
            $this->assertArrayHasKey($index, $actualCalls[$methodName]);
            $this->assertEquals($param, $actualCalls[$methodName][$index]);
        }
    }

    /**
     * @param string $file
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected function compileContainer($file, ContainerBuilder $container = null)
    {
        $container         = $container ?: $this->createContainer();
        $cacheExtension    = new DoctrineCacheExtension();
        $securityExtension = new SecurityExtension();

        //$container->addCompilerPass(new AclCacheCompilerPass());
        $container->registerExtension($cacheExtension);
        $container->registerExtension($securityExtension);

        //$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Fixtures/config'));

        //$loader->load('security.yml');

        $this->loadFromFile($container, $file);

        $container->compile();

        return $container;
    }
}