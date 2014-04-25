<?php
/**
 *
 */

namespace Doctrine\Bundle\DoctrineCacheBundle\Logger;


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
        );

    }

    public function log($cacheName, $logType, $log)
    {
        $this->logs[$cacheName][$logType][] = $log;
        $this->updateTotals($log);
    }

    public function updateTotals($log)
    {
        $this->totals['count']++;
        $this->totals['duration'] += $log['duration'];

        if ($log['type'] === 'fetch') {
            $this->totals['hit']  += $log['result'] === false ? 0 : 1;
            $this->totals['miss'] += $log['result'] === false ? 1 : 0;
        }

        if ($log['type'] === 'save') {
            $this->totals['write']++;
        }
    }

    /**
     * @param array $logs
     * @return LogMaster
     */
    public function setLogs($logs)
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
     * @return LogMaster
     */
    public function setTotals($totals)
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
