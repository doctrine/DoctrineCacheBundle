Cache Provider Configuration
============================

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
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

    .. code-block:: xml

        <!-- app/config/config.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <dic:container xmlns="http://doctrine-project.org/schemas/symfony-dic/cache"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:srv="http://symfony.com/schema/dic/services"
            xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd
                http://doctrine-project.org/schemas/symfony-dic/cache http://doctrine-project.org/schemas/symfony-dic/cache/doctrine_cache-1.0.xsd">

        <srv:container>
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
        </srv:container>

See :doc:`reference` for all the specific configurations.
