DoctrineCacheBundle
===================

Symfony Bundle for Doctrine Cache.

Master: [![Build Status](https://secure.travis-ci.org/doctrine/DoctrineCacheBundle.svg?branch=master)](https://travis-ci.org/doctrine/DoctrineCacheBundle)

Master: [![Coverage Status](https://coveralls.io/repos/doctrine/DoctrineCacheBundle/badge.png?branch=master)](https://coveralls.io/r/doctrine/DoctrineCacheBundle?branch=master)

## Deprecation warning

This bundle is deprecated; it will not be updated for Symfony 5. If you want to
use doctrine/cache in Symfony, please configure the services manually. When
using Symfony, we no longer recommend configuring doctrine/cache through this
bundle. Instead, you should use symfony/cache for your cache needs. However, the
deprecation does not extend to doctrine/cache, you'll be able to use those
classes as you did so far.

## Installation

1. Add this bundle to your project as a composer dependency:

  ```bash
  composer require doctrine/doctrine-cache-bundle
  ```

2. Add this bundle in your application kernel:

    ```php
    // app/AppKernel.php
    public function registerBundles()
    {
        // ...
        $bundles[] = new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle();

        return $bundles;
    }
    ```

Read the [documentation](https://www.doctrine-project.org/projects/doctrine-cache-bundle/en/stable/usage.html) to learn how to configure and
use your own cache providers.
