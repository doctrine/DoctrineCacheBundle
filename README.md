DoctrineCacheBundle
===================

Symfony Bundle for Doctrine Cache.

Master: [![Build Status](https://secure.travis-ci.org/doctrine/DoctrineCacheBundle.png?branch=master)](http://travis-ci.org/doctrine/DoctrineCacheBundle)

Master: [![Coverage Status](https://coveralls.io/repos/doctrine/DoctrineCacheBundle/badge.png?branch=master)](https://coveralls.io/r/doctrine/DoctrineCacheBundle?branch=master)

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

Read the [documentation](Resources/doc/index.rst) to learn how to configure and
use your own cache providers.
