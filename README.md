DoctrineCacheBundle
===================

Symfony2 Bundle for Doctrine Cache

Master: [![Build Status](https://secure.travis-ci.org/doctrine/DoctrineCacheBundle.png?branch=master)](http://travis-ci.org/doctrine/DoctrineCacheBundle)

Master: [![Coverage Status](https://coveralls.io/repos/doctrine/DoctrineCacheBundle/badge.png?branch=master)](https://coveralls.io/r/doctrine/DoctrineCacheBundle?branch=master)

## Installation

Installing this bundle can be done through these simple steps:

1. Add this bundle to your project as a composer dependency:
```javascript
    // composer.json
    {
        // ...
        require: {
            // ...
            "doctrine/doctrine-cache-bundle": "~1.0"
        }
    }
```

2. Add this bundle in your application kernel:
```php
    // application/ApplicationKernel.php
    public function registerBundles()
    {
        // ...
        $bundles[] = new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle();

        return $bundles;
    }
```

3. Check if the bundle is configured correctly:
```xml
<!--  {# application/config/doctrine_cache.xml #} -->

<doctrine-cache>
     <provider name="my_apc_metadata_cache">
        <type>apc</type>
        <namespace>metadata_cache_ns</namespace>
     </provider>
    <provider name="my_apc_query_cache" namespace="query_cache_ns">
        <apc/>
    <provider>
</doctrine-cache>
```

```yml
# {# application/config/doctrine_cache.yml #}

doctrine_cache:
    providers:
        my_apc_metadata_cache:
            type: apc
            namespace: metadata_cache_ns
        my_apc_query_cache:
            namespace: query_cache_ns
            apc: ~
```

## Usage
Simply use `doctrine_cache.providers.{provider_name}` to inject it into the desired service.

Check the following sample:

```php
$apcCache   = $this->container->get('doctrine_cache.providers.my_apc_cache');
$arrayCache = $this->container->get('doctrine_cache.providers.my_array_cache');

```

## Provider configuration
```xml
<!--  {# application/config/doctrine_cache.xml #}  -->

<doctrine-cache>
     <provider name="my_memcached_cache">
         <memcache>
             <server host="memcached01.ss" port="11211"/>
             <server>
                <host>memcached01.ss</host>
                <port>11211</port>
             </server>
         </memcache>
     </provider>

     <provider name="my_riak_cache">
         <riak host="localhost" port="8087">
             <bucket-name>my_bucket</bucket-name>
             <bucket-property-list>
                 <allow-multiple>false</allow-multiple>
                 <n-value>1</n-value>
             </bucket-property-list>
         </riak>
     </provider>
</doctrine-cache>
```

```yml
# {# application/config/doctrine_cache.yml #}

doctrine_cache:
    providers:
        my_memcached_cache:
            memcached:
                servers:
                    memcached01.ss: 11211
                    memcached02.ss: 
                        port: 11211
        my_riak_cache:
            riak:
                host: localhost
                port: 8087
                bucket_name: my_bucket
                bucket_property_list:
                    allow_multiple: false
                    n_value: 1
```

###### See [Cache providers](#cache-providers) for all supported cache provider and its specific configurations


## Service aliases
```xml
<!--   {# application/config/doctrine_cache.xml #} -->

<doctrine-cache>
    <alias key="cache_apc">my_apc_cache</alias>

    <provider name="my_apc_cache">
        <type>apc</type>
        <namespace>my_apc_cache_ns</namespace>
        <alias>apc_cache</alias>
    </provider>
</doctrine-cache>
```

```yml
# {# application/config/doctrine_cache.yml #}

doctrine_cache:
    aliases:
        cache_apc: my_apc_cache

    providers:
        my_apc_cache:
            type: apc
            namespace: my_apc_cache_ns
            aliases:
                - apc_cache
```

You can  access the cache providers by using created aliases:

```php
$apcCache  = $this->container->get('apc_cache');
$cacheApc  = $this->container->get('cache_apc');
```


## Custom providers
Is possible to register a custom cache driver
```xml
<!--   {# application/config/doctrine_cache.xml #} -->

<srv:services>
    <srv:service id="my_custom_provider_service" class="MyCustomType">
        <!-- ... -->
    </srv:service>
 </srv:services>

<doctrine-cache>
    <!-- register your custom cache provider -->
    <custom-provider type="my_custom_type">
        <prototype>my_custom_provider_service</prototype>
        <definition-class>MyCustomTypeDefinition</definition-class> <!-- optional configuration -->
    </custom-provider>

     <provider name="my_custom_type_provider">
        <my_custom_type>
             <config-foo>foo</config-foo>
             <config-bar>bar</config-bar>
         </my_custom_type>
     </provider>
</doctrine-cache>
```

```yml
# {# application/config/doctrine_cache.yml #}

services:
    my_custom_provider_service:
        class: "MyCustomType"
        # ...

doctrine_cache:
    custom_providers:
        my_custom_type:
            prototype:  "my_custom_provider_service"
            definition_class: "MyCustomTypeDefinition" # optional configuration

    providers:
        my_custom_type_provider:
            my_custom_type:
                config_foo: "foo"
                config_bar: "bar"
```
###### Definition class is a optional configuration that will parse option arguments given to your custom cache driver See [CacheDefinition](https://github.com/doctrine/DoctrineCacheBundle/blob/master/DependencyInjection/Definition/CacheDefinition.php)


## Service parameter
Is possible to configure a cache provider using a specific connection/bucket/collection
```xml
<!--   {# application/config/doctrine_cache.xml #} -->

<srv:services>
    <srv:service id="my_riak_connection_service" class="Riak\Connection">
        <!-- ... -->
    </srv:service>

    <srv:service id="my_riak_bucket_service" class="Riak\Bucket">
        <!-- ... -->
    </srv:service>

    <srv:service id="my_memcached_connection_service" class="Memcached">
        <!-- ... -->
    </srv:service>
 </srv:services>

<doctrine-cache>
     <provider  name="service_bucket_riak_provider">
         <riak bucket-id="my_riak_bucket_service"/>
     </provider>

     <provider name="service_connection_riak_provider">
         <riak connection-id="my_riak_connection_service">
             <bucket-name>my_bucket_name</bucket-name>
         </riak>
     </provider>

     <provider name="service_connection_memcached_provider">
         <memcached connection-id="my_memcached_connection_service"/>
     </provider>
</doctrine-cache>
```

```yml
# {# application/config/doctrine_cache.yml #}

services:
    my_riak_connection_service:
        class: "Riak\Connection"
        # ...

    my_riak_bucket_service:
        class: "Riak\Bucket"
        # ...

    my_memcached_connection_service:
        class: "Memcached"
        # ...

doctrine_cache:
    providers:
        service_bucket_riak_provider:
            riak:
                bucket_id : "my_riak_bucket_service"

        service_connection_riak_provider:
            riak:
                connection_id: "my_riak_connection_service"
                bucket_name: "my_bucket_name"

        service_connection_memcached_provider:
            memcached:
                connection_id: "my_memcached_connection_service"

```

###### See [Cache providers](#cache-providers) for all specific configurations



## Symfony acl cache
```xml
<!--   {# application/config/doctrine_cache.xml #} -->

<doctrine-cache>
    <acl-cache id="doctrine_cache.providers.acl_apc_provider"/>

    <provider name="acl_apc_provider" type="apc"/>
</doctrine-cache>
```

```yml
# {# application/config/doctrine_cache.yml #}

doctrine_cache:
    acl_cache:
        id: 'doctrine_cache.providers.acl_apc_provider'
    providers:
        acl_apc_provider:
            type: 'apc'

```

Check the following sample:
```php
/** @var $aclCache Symfony\Component\Security\Acl\Model\AclCacheInterface */
$aclCache = $this->container->get('security.acl.cache');

```


## Cache providers

#### apc
#### array
#### xcache
#### wincache
#### zenddata
#### memcache
    - connection_id - Memcache connection service id
    - servers       - Server list
        - server
            - host - memcache host
            - port - memcache port
#### memcached
    - connection_id - Memcache connection service id
    - servers       - Server list
        - server
            - host - memcached host
            - port - memcached port
#### redis
    - connection_id - Redis connection service id
    - host          - redis host
    - port          - redis port
#### couchbase
    - hostnames    - couchbase hostname list
    - bucket_name  - couchbase bucket name
    - username     - couchbase username
    - password     - couchbase password
#### php_file
    - extension    - file extension
    - directory    - cache directory
#### file_system
    - extension    - file extension
    - directory    - cache directory
#### mongodb
    - connection_id     - MongoClient service id
    - collection_id     - MongoCollection service id
    - server            - mongodb server uri
    - database_name     - mongodb database name
    - collection_name   - mongodb collection name
#### riak
    - connection_id                 - Riak\Connection service id
    - bucket_id                     - Riak\Bucket service id
    - host                          - riak host
    - port                          - riak port
    - bucket_name                   - riak bucket name
    - bucket_property_list          - riak bucket configuration (property list)
        - allow_multiple: false     - riak bucket allow multiple configuration
        - n_value: 1                - riak bucket n-value configuration


Check the [doctrine-cache documentation Page](http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/caching.html) for a better understanding.
