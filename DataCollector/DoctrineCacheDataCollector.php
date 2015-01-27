<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster;

/**
 * Symfony data collector for the debug profiler.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class DoctrineCacheDataCollector extends DataCollector
{
    /** @var \Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster */
    private $logMaster;

    /**
     * @param \Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster $logMaster
     */
    public function __construct(LogMaster $logMaster)
    {
        $this->logMaster = $logMaster;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'logs'   => $this->logMaster->getLogs(),
            'totals' => $this->logMaster->getTotals(),
        );
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        return $this->data['logs'];
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->data['totals'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'doctrine_cache';
    }
}
