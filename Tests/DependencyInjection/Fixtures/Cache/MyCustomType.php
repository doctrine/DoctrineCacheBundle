<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
