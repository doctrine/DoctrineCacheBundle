<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Logger;

use Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster;

/**
 * Unit tests for LogMaster class.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class LogMasterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster
     */
    private $logMaster;

    /**
     * @var array
     */
    private $logs;

    /**
     * @var array
     */
    private $totals;

    /**
     * @var string
     */
    private $cacheName = 'test_cache';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->logMaster = new LogMaster();

        $start = (float)1398787520.4424;
        $end   = $start + 1000;
        $id    = 'test_id';
        $data  = 'hello world';

        $this->logs = array(
            'fetch' => array(
                'type' => 'fetch',
                'start' => $start,
                'duration' => $end - $start,
                'id' => $id,
                'data' => $data,
                'success' => true,
            ),
            'save' => array(
                'type' => 'save',
                'start' => $start,
                'duration' => $end - $start,
                'id' => $id,
                'data' => $data,
                'success' => true,
                'lifetime' => 5,
            ),
            'delete' => array(
                'type' => 'delete',
                'start' => $start,
                'duration' => $end - $start,
                'id' => $id,
                'success' => true,
            ),
        );

        $this->totals = array(
            'count' => 3,
            'duration' => 3000.0,
            'hit' => 0,
            'miss' => 1,
            'write' => 1,
            'delete' => 1,
        );
    }

    /**
     * Data provider for testLog.
     *
     * @see testLog()
     *
     * @return array
     */
    public function logDataProvider()
    {
        $this->setUp();

        return array(
            array($this->cacheName, 'save', $this->logs['save']),
            array($this->cacheName, 'fetch', $this->logs['fetch']),
            array($this->cacheName, 'delete', $this->logs['delete']),
        );
    }

    /**
     * Tests logging a cache request.
     *
     * @param string $cache
     * @param string $type
     * @param array $log
     *
     * @dataProvider logDataProvider
     */
    public function testLog($cache, $type, $log)
    {
        $this->logMaster->log($cache, $type, $log);
        $logs = $this->logMaster->getLogs();
        $this->assertEquals($this->logs[$type], $logs[$cache][$type][0]);
    }

    /**
     * Tests updating totals.
     */
    public function testTotals()
    {
        foreach ($this->logs as $log) {
            $this->logMaster->updateTotals($log);
        }
        $totals = $this->logMaster->getTotals();
        $this->assertEquals($this->totals, $totals);
    }

    /**
     * Tests manually setting logs.
     */
    public function testSetLogs()
    {
        $this->logMaster->setLogs($this->logs);
        $this->assertEquals($this->logs, $this->logMaster->getLogs());
    }

    /**
     * Tests manually setting totals.
     */
    public function testSetTotals()
    {
        $this->logMaster->setTotals($this->totals);
        $this->assertEquals($this->totals, $this->logMaster->getTotals());
    }
}
