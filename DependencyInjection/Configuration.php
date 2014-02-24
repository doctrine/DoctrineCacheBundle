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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Cache Bundle Configuration
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('doctrine_cache', 'array');

        $node
            ->fixXmlConfig('provider')
            ->children()
                ->arrayNode('providers')
                ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function ($v) {
                                return ( ! isset($v['type']));
                            })
                            ->then(function ($val) {
                                $copy = $val;

                                if (isset($copy['namespace'])) {
                                    unset($copy['namespace']);
                                }

                                $val['type'] = key($copy);

                                return $val;
                            })
                        ->end()
                        ->children()
                            ->scalarNode('namespace')->defaultNull()->end()
                            ->scalarNode('type')->defaultNull()->end()
                            ->append($this->addMemcachedNode())
                            ->append($this->addMemcacheNode())
                            ->append($this->addCouchbaseNode())
                            ->append($this->addFileSystemNode())
                            ->append($this->addPhpFileNode())
                            ->append($this->addMongoNode())
                            ->append($this->addRedisNode())
                            ->append($this->addRiakNode())
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }

    /**
     * Build memcache node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The bucket property list tree builder
     */
    private function addMemcacheNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('memcache');
        $host    = '%doctrine_cache.memcache.host%';
        $port    = '%doctrine_cache.memcache.port%';

        $node
            ->fixXmlConfig('server')
            ->children()
                ->arrayNode('servers')
                ->useAttributeAsKey('host')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function ($v) {
                                return is_scalar($v);
                            })
                            ->then(function ($val) {
                                return array('port' => $val);
                            })
                        ->end()
                        ->children()
                            ->scalarNode('host')->defaultValue($host)->end()
                            ->scalarNode('port')->defaultValue($port)->end()
                        ->end()
                    ->end()
                    ->defaultValue(array($host => array(
                        'host' => $host,
                        'port' => $port
                    )))
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build memcached node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The bucket property list tree builder
     */
    private function addMemcachedNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('memcached');
        $host    = '%doctrine_cache.memcached.host%';
        $port    = '%doctrine_cache.memcached.port%';

        $node
            ->fixXmlConfig('server')
            ->children()
                ->arrayNode('servers')
                ->useAttributeAsKey('host')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifTrue(function ($v) {
                                return is_scalar($v);
                            })
                            ->then(function ($val) {
                                return array('port' => $val);
                            })
                        ->end()
                        ->children()
                            ->scalarNode('host')->defaultValue($host)->end()
                            ->scalarNode('port')->defaultValue($port)->end()
                        ->end()
                    ->end()
                    ->defaultValue(array($host => array(
                        'host' => $host,
                        'port' => $port
                    )))
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build redis node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The bucket property list tree builder
     */
    private function addRedisNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('redis');

        $node
            ->children()
                ->scalarNode('host')->defaultValue('%doctrine_cache.redis.host%')->end()
                ->scalarNode('port')->defaultValue('%doctrine_cache.redis.port%')->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build riak node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The bucket property list tree builder
     */
    private function addRiakNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('riak');

        $node
            ->children()
                ->scalarNode('host')->defaultValue('%doctrine_cache.riak.host%')->end()
                ->scalarNode('port')->defaultValue('%doctrine_cache.riak.port%')->end()
                ->scalarNode('bucket_name')->defaultValue('doctrine_cache')->end()
                ->arrayNode('bucket_property_list')
                    ->children()
                        ->scalarNode('allow_multiple')->defaultNull()->end()
                        ->scalarNode('n_value')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build couchbase node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The bucket property list tree builder
     */
    private function addCouchbaseNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('couchbase');

        $node
            ->fixXmlConfig('hostname')
            ->children()
                ->arrayNode('hostnames')
                    ->prototype('scalar')->end()
                    ->defaultValue(array('%doctrine_cache.couchbase.hostnames%'))
                ->end()
                ->scalarNode('username')->defaultNull()->end()
                ->scalarNode('password')->defaultNull()->end()
                ->scalarNode('bucket_name')->defaultValue('doctrine_cache')->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build mongodb node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The bucket property list tree builder
     */
    private function addMongoNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('mongodb');

        $node
            ->children()
                ->scalarNode('database_name')->defaultValue('doctrine_cache')->end()
                ->scalarNode('collection_name')->defaultValue('doctrine_cache')->end()
                ->scalarNode('server')->defaultValue('%doctrine_cache.mongodb.server%')->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build php_file node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The bucket property list tree builder
     */
    private function addPhpFileNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('php_file');

        $node
            ->children()
                ->scalarNode('directory')->defaultNull()->end()
                ->scalarNode('extension')->defaultValue('%kernel.cache_dir%/doctrine/cache/phpfile')->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Build file_system node configuration definition
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The bucket property list tree builder
     */
    private function addFileSystemNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('file_system');

        $node
            ->children()
                ->scalarNode('directory')->defaultNull()->end()
                ->scalarNode('extension')->defaultValue('%kernel.cache_dir%/doctrine/cache/filesystem')->end()
            ->end()
        ;

        return $node;
    }
}
