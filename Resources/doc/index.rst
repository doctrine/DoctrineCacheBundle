DoctrineCacheBundle
===================

Since the version 1.3 of DoctrineBundle the DoctrineCacheBundle is included by default.

The DoctrineCacheBundle is used to allow different systems of cache in your application using the `Doctrine Cache`_ library.

Setup and Configuration
-----------------------

Doctrine cache for Symfony is maintained in the `DoctrineCacheBundle`_.
The bundle uses external `Doctrine Cache`_ library.

Follow these steps to install the bundle and the library in the Symfony
Standard edition. Add the following to your ``composer.json`` file:

.. code-block:: json

    {
        "require": {
            "doctrine/doctrine-cache-bundle": "1.0.*"
        }
    }

Update the vendor libraries:

.. code-block:: bash

    $ php composer.phar update doctrine/doctrine-cache-bundle

If everything worked, the ``DoctrineCacheBundle`` can now be found
at ``vendor/doctrine/doctrine-cache-bundle``.

.. note::

    ``DoctrineCacheBundle`` installs
    `Doctrine Cache`_ library. The library can be found
    at ``vendor/doctrine/cache``.

Finally, register the Bundle ``DoctrineCacheBundle`` in ``app/AppKernel.php``.

.. code-block:: php

    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        );
    
        // ...
    }

ORM caching configuration
-------------------------

Some configurations of the ORM can use cache to prevent an excessive number of requests, read the `Caching Drivers`_.

.. code-block:: yaml

    # app/config/config.yml
    doctrine:
        orm:
            metadata_cache_driver:
                # will load doctrine_cache.providers.metadata_cache_driver
                cache_provider: metadata_cache_driver
            query_cache_driver:
                cache_provider: query_cache_driver
            result_cache_driver:
                # if you're using a version < 1.3 of the DoctrineBundle
                # you can use the "service" type
                type: service
                id: doctrine_cache.providers.result_cache_driver

    doctrine_cache:
        providers:
            metadata_cache_driver:
                type: apc
            query_cache_driver:
                type: apc
            result_cache_driver:
                type: apc


.. _`Caching Drivers`: http://symfony.com/doc/current/reference/configuration/doctrine.html#caching-drivers
.. _`Doctrine Cache`: http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/caching.html