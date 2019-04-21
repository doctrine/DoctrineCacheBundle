<?php

namespace Doctrine\Bundle\DoctrineCacheBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony Bundle for Doctrine Cache
 */
class DoctrineCacheBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function registerCommands(Application $application)
    {
    }
}
