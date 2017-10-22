Service Parameter
=================

Cache providers can be also configured using a specific connection, bucket or
collection. Example:

.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            # ...

            my_memcached_connection_service:
                class: "Memcached"
                # ...

        # app/config/config.yml
        doctrine_cache:
            providers:
                service_connection_memcached_provider:
                    memcached:
                        connection_id: "my_memcached_connection_service"

    .. code-block:: xml

        <!-- app/config/config.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <dic:container xmlns="http://doctrine-project.org/schemas/symfony-dic/cache"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:srv="http://symfony.com/schema/dic/services"
            xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd
                http://doctrine-project.org/schemas/symfony-dic/cache http://doctrine-project.org/schemas/symfony-dic/cache/doctrine_cache-1.0.xsd">

        <srv:container>
            <srv:services>
                <srv:service id="my_memcached_connection_service" class="Memcached">
                    <!-- ... -->
                </srv:service>
             </srv:services>

            <doctrine-cache>
                 <provider name="service_connection_memcached_provider">
                     <memcached connection-id="my_memcached_connection_service"/>
                 </provider>
            </doctrine-cache>
        </srv:container>

See :doc:`reference` for all the specific configurations.
