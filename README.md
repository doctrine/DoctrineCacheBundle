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
            "doctrine/doctrine-cache-bundle": "dev-master"
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

#### application/config/doctrine_cache.xml
```xml
<?xml version="1.0" ?>
<!-- ... -->
<srv:containe>
    <srv:services>
        <srv:service id="my_riak_bucket_service" class="Riak\Bucket">
            <!-- ... -->
        </srv:service>
        <srv:service id="my_custom_provider_service" class="MyCustomType">
            <!-- ... -->
        </srv:service>
    </srv:services>
</srv:container>

<doctrine-cache>
     <alias key="apc">my_apc_cache</alias>

     <custom-provider type="my_custom_type">
        <prototype>my_custom_provider_service</prototype>
     </custom-provider>

     <provider name="my_apc_cache" type="apc" namespace="my_apc_ns"/>

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
         <alias>riak</alias>
         <alias>riak_cache</alias>
     </provider>

     <provider name="service_bucket_riak_provider">
         <riak bucket-id="my_riak_bucket_service"/>
     </provider>
     
     <provider name="my_custom_type_provider">
        <my_custom_type/>
     </provider>
</doctrine-cache>
```

#### application/config/doctrine_cache.yml
```yml
# ...

services:
    my_riak_bucket_service:
        class: "Riak\Bucket"
        arguments: ["..."]
    my_custom_provider_service:
        class: "MyCustomType"
        # ...


doctrine_cache:
    aliases:
        apc: my_apc_cache

    my_custom_type:
        prototype:  "my_custom_provider_service"

    providers:
        my_apc_cache:
            type: apc
            namespace: my_apc_ns
        my_memcached_cache:
            memcached:
                servers:
                    memcached01.ss: 11211
                    memcached02.ss: 
                        port: 11211
        my_riak_cache:
            aliases:
                - riak
                - riak_cache
            riak:
                host: localhost
                port: 8087
                bucket_name: my_bucket
                bucket_property_list:
                    allow_multiple: false
                    n_value: 1
        service_bucket_riak_provider:
            riak:
                bucket_id : "my_riak_bucket_service"
        my_custom_type_provider:
            my_custom_type: ~
```

4. Simply use `doctrine_cache.providers.{provider_name}` to inject it into the desired service.


## Usage

Check the following sample:


```php
$apcCache   = $this->container->get('doctrine_cache.providers.my_apc_cache');
$riakCache  = $this->container->get('doctrine_cache.providers.my_riak_cache');
$memCache   = $this->container->get('doctrine_cache.providers.my_memcached_cache');

```

You can also access the cache providers by using created aliases:

```php
$riakCache  = $this->container->get('riak_cache');
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
