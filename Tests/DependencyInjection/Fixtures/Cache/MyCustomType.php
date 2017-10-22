<?php

declare(strict_types=1);

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\DependencyInjection\Fixtures\Cache;

use Doctrine\Common\Cache\ArrayCache;

class MyCustomType extends ArrayCache
{
    public $configs;

    public function addConfig($name, $value)
    {
        $this->configs[$name] = $value;
    }
}
