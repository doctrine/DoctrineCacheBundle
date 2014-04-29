<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Logger;

/**
 * Central storage for cache logs during this request.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class LogMaster
{
    /**
     * @var array
     */
    protected $logs;

    /**
     * @var array
     */
    protected $totals;

    public function __construct()
    {
        $this->logs   = array();
        $this->totals = array(
            'count'    => 0,
            'duration' => 0,
            'hit'      => 0,
            'miss'     => 0,
            'write'    => 0,
            'delete'   => 0,
        );
    }

    /**
     * Store cache log.
     *
     * @param string $cacheName
     * @param string $logType
     * @param array $log
     */
    public function log($cacheName, $logType, array $log)
    {
        $this->logs[$cacheName][$logType][] = $log;
        $this->updateTotals($log);
    }

    /**
     * Update log statistics.
     *
     * @param array $log
     */
    public function updateTotals(array $log)
    {
        $this->totals['count']++;
        $this->totals['duration'] += $log['duration'];

        switch ($log['type']) {
            case 'fetch':
                $this->totals['hit']  += $log['success'] ? 0 : 1;
                $this->totals['miss'] += $log['success'] ? 1 : 0;
                break;

            case 'save':
                $this->totals['write']++;
                break;

            case 'delete':
                $this->totals['delete']++;
                break;
        }
    }

    /**
     * @param array $logs
     *
     * @return LogMaster
     */
    public function setLogs(array $logs)
    {
        $this->logs = $logs;

        return $this;
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param array $totals
     *
     * @return LogMaster
     */
    public function setTotals(array $totals)
    {
        $this->totals = $totals;

        return $this;
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->totals;
    }
}
