Service Aliases
===============

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
        doctrine_cache:
            aliases:
                cache_apc: my_apc_cache

            providers:
                my_apc_cache:
                    type: apc
                    namespace: my_apc_cache_ns
                    aliases:
                        - apc_cache

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
                <alias key="cache_apc">my_apc_cache</alias>

                <provider name="my_apc_cache">
                    <type>apc</type>
                    <namespace>my_apc_cache_ns</namespace>
                    <alias>apc_cache</alias>
                </provider>
            </doctrine-cache>
        </srv:container>

You can access the cache providers by using created aliases::

    $apcCache = $this->container->get('apc_cache');
    $cacheApc = $this->container->get('cache_apc');
