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

use Doctrine\Bundle\DoctrineCacheBundle\Tests\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;
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

    public function testParameters()
    {
        $container      = $this->createContainer();
        $cacheExtension = new DoctrineCacheExtension();

        $cacheExtension->load(array(), $container);

        $this->assertTrue($container->hasParameter('doctrine_cache.apc.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.array.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.couchbase.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.file_system.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.memcached.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.memcache.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.mongodb.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.php_file.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.redis.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.riak.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.sqlite3.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.void.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.xcache.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.wincache.class'));
        $this->assertTrue($container->hasParameter('doctrine_cache.zenddata.class'));
    }

    public function testBasicCache()
    {
        $container = $this->compileContainer('basic');
        $drivers   = array(
            'basic_apc_provider'         => '%doctrine_cache.apc.class%',
            'basic_array_provider'       => '%doctrine_cache.array.class%',
            'basic_void_provider'        => '%doctrine_cache.void.class%',
            'basic_xcache_provider'      => '%doctrine_cache.xcache.class%',
            'basic_wincache_provider'    => '%doctrine_cache.wincache.class%',
            'basic_zenddata_provider'    => '%doctrine_cache.zenddata.class%',
            'basic_ns_zenddata_provider' => '%doctrine_cache.zenddata.class%',

            'basic_apc_provider2'         => '%doctrine_cache.apc.class%',
            'basic_array_provider2'       => '%doctrine_cache.array.class%',
            'basic_void_provider2'        => '%doctrine_cache.void.class%',
            'basic_xcache_provider2'      => '%doctrine_cache.xcache.class%',
            'basic_wincache_provider2'    => '%doctrine_cache.wincache.class%',
            'basic_zenddata_provider2'    => '%doctrine_cache.zenddata.class%',
        );

        foreach ($drivers as $key => $value) {
            $this->assertCacheProvider($container, $key, $value);
        }
    }

    public function testBasicConfigurableCache()
    {
        $container = $this->compileContainer('configurable');
        $drivers   = array(
            'configurable_chain_provider' => array(
                '%doctrine_cache.chain.class%'
            ),
            'configurable_couchbase_provider' => array(
                '%doctrine_cache.couchbase.class%'
            ),
            'configurable_filesystem_provider' => array(
                '%doctrine_cache.file_system.class%'
            ),
            'configurable_memcached_provider' => array(
                '%doctrine_cache.memcached.class%', array('setMemcached' => array())
            ),
            'configurable_memcache_provider' => array(
                '%doctrine_cache.memcache.class%', array('setMemcache' => array())
            ),
            'configurable_mongodb_provider' => array(
                '%doctrine_cache.mongodb.class%'
            ),
            'configurable_phpfile_provider' => array(
                '%doctrine_cache.php_file.class%'
            ),
            'configurable_redis_provider' => array(
                '%doctrine_cache.redis.class%', array('setRedis' => array())
            ),
            'configurable_riak_provider' => array(
                '%doctrine_cache.riak.class%'
            ),
            'configurable_sqlite3_provider' => array(
                '%doctrine_cache.sqlite3.class%'
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
                '%doctrine_cache.memcached.class%', array('setMemcached' => array())
            ),
            'configurable_memcache_provider' => array(
                '%doctrine_cache.memcache.class%', array('setMemcache' => array())
            ),
            'configurable_redis_provider' => array(
                '%doctrine_cache.redis.class%', array('setRedis' => array())
            ),
            'configurable_mongodb_provider' => array(
                '%doctrine_cache.mongodb.class%'
            ),
            'configurable_riak_provider' => array(
                '%doctrine_cache.riak.class%'
            ),
            'configurable_filesystem_provider' => array(
                '%doctrine_cache.file_system.class%'
            ),
            'configurable_phpfile_provider' => array(
                '%doctrine_cache.php_file.class%'
            ),
            'configurable_couchbase_provider' => array(
                '%doctrine_cache.couchbase.class%'
            ),
            'configurable_memcached_provider_type' => array(
                '%doctrine_cache.memcached.class%', array('setMemcached' => array())
            ),
            'configurable_memcache_provider_type' => array(
                '%doctrine_cache.memcache.class%', array('setMemcache' => array())
            ),
            'configurable_redis_provider_type' => array(
                '%doctrine_cache.redis.class%', array('setRedis' => array())
            ),
            'configurable_mongodb_provider_type' => array(
                '%doctrine_cache.mongodb.class%'
            ),
            'configurable_riak_provider_type' => array(
                '%doctrine_cache.riak.class%'
            ),
            'configurable_filesystem_provider_type' => array(
                '%doctrine_cache.file_system.class%'
            ),
            'configurable_phpfile_provider_type' => array(
                '%doctrine_cache.php_file.class%'
            ),
            'configurable_couchbase_provider_type' => array(
                '%doctrine_cache.couchbase.class%'
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
                '%doctrine_cache.riak.class%'
            ),
            'service_connection_riak_provider' => array(
                '%doctrine_cache.riak.class%'
            ),
            'service_connection_memcached_provider' => array(
                '%doctrine_cache.memcached.class%'
            ),
            'service_connection_memcache_provider' => array(
                '%doctrine_cache.memcache.class%'
            ),
            'service_connection_redis_provider' => array(
                '%doctrine_cache.redis.class%'
            ),
            'service_connection_mongodb_provider' => array(
                '%doctrine_cache.mongodb.class%'
            ),
            'service_collection_mongodb_provider' => array(
                '%doctrine_cache.mongodb.class%'
            ),
            'service_connection_sqlite3_provider' => array(
                '%doctrine_cache.sqlite3.class%'
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

        $this->assertTrue($container->hasDefinition('doctrine_cache.security.acl.cache'));

        $definition = $container->getDefinition('doctrine_cache.security.acl.cache');

        $this->assertEquals('Doctrine\Bundle\DoctrineCacheBundle\Acl\Model\AclCache', $definition->getClass());
        $this->assertCount(2, $definition->getArguments());
        $this->assertEquals('doctrine_cache.providers.acl_apc_provider', (string) $definition->getArgument(0));
        $this->assertEquals('security.acl.permission_granting_strategy', (string) $definition->getArgument(1));
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
        $container      = $container ?: $this->createContainer();
        $cacheExtension = new DoctrineCacheExtension();

        $container->registerExtension($cacheExtension);

        $compilerPassConfig = $container->getCompilerPassConfig();

        $compilerPassConfig->setOptimizationPasses(array(new ResolveDefinitionTemplatesPass()));
        $compilerPassConfig->setRemovingPasses(array());

        $this->loadFromFile($container, $file);

        $container->compile();

        return $container;
    }
}
