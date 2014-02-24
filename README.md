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
<doctrine-cache>
     <alias key="apc">my_apc_cache</alias>

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
</doctrine-cache>
```

#### application/config/doctrine_cache.yml
```yml
# ...
doctrine_cache:
    aliases:
        apc: my_apc_cache
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

- apc
- array
- xcache
- wincache
- zenddata
- memcache
    - servers ``# server list``
        - server
            - host ``# memcache host``
            - port ``# memcache port``
- memcached
    - servers ``# server list``
        - server
            - host ``# memcached host``
            - port ``# memcached port``
- redis
    - host ``# redis host``
    - port ``# redis port``
- couchbase
    - hostnames    ``# couchbase hostname list``
    - bucket_name  ``# couchbase bucket name``
    - username     ``# couchbase username``
    - password     ``# couchbase password``
- php_file
    - extension    ``# file extension``
    - directory    ``# cache directory``
- file_system
    - extension    ``# file extension``
    - directory    ``# cache directory``
- mongodb
    - server            ``# mongodb server uri``
    - database_name     ``# mongodb database name``
    - collection_name   ``# mongodb collection name``
- riak
    - host                          ``# riak host``
    - port                          ``# riak port``
    - bucket_name                   ``# riak bucket name``
    - bucket_property_list          ``# riak bucket configuration (property list)``
        - allow_multiple: false     ``# riak bucket allow multiple configuration``
        - n_value: 1                ``# riak bucket n-value configuration``


Check the [doctrine-cache documentation Page](http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/caching.html) for a better understanding.
