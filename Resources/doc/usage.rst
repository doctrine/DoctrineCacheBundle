Usage
=====

First, configure your cache providers under the ``doctrine_cache`` configuration
option:

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
        doctrine_cache:
            providers:
                my_apc_metadata_cache:
                    type: apc
                    namespace: metadata_cache_ns
                my_apc_query_cache:
                    namespace: query_cache_ns
                    apc: ~

    .. code-block:: yaml

        <!-- app/config/config.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:doctrine-cache="http://doctrine-project.org/schemas/symfony-dic/cache"
            xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd
                http://doctrine-project.org/schemas/symfony-dic/cache http://doctrine-project.org/schemas/symfony-dic/cache/doctrine_cache-1.0.xsd">

            <doctrine-cache:doctrine-cache>
                 <doctrine-cache:provider name="my_apc_metadata_cache">
                    <doctrine-cache:type>apc</doctrine-cache:type>
                    <doctrine-cache:namespace>metadata_cache_ns</doctrine-cache:namespace>
                 </doctrine-cache:provider>
                <doctrine-cache:provider name="my_apc_query_cache" namespace="query_cache_ns">
                    <doctrine-cache:apc/>
                <doctrine-cache:provider>
            </doctrine-cache:doctrine-cache>
        </container>

Then, use ``doctrine_cache.providers.{provider_name}`` to inject each cache
provider into the desired service::

    $apcCache = $this->container->get('doctrine_cache.providers.my_apc_cache');
    $arrayCache = $this->container->get('doctrine_cache.providers.my_array_cache');
