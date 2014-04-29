<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Logger;

use Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster;
use Doctrine\Common\Cache\Cache;

/**
 * Proxy for logging cache requests.
 *
 * @author Alan Doucette <dragonwize@gmail.com>
 */
class CacheLoggerProxy implements Cache
{
    /**
     * @var string
     */
    protected $serviceId;

    /**
     * @var string
     */
    protected $cacheName;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var \Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster
     */
    protected $logMaster;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * @param string $serviceId
     * @param string $class
     * @param array  $args
     */
    public function __construct($serviceId, $class, array $args = array())
    {
        $this->serviceId = $serviceId;
        $this->cacheName = substr($serviceId, strrpos($serviceId, '.') + 1);
        $this->class     = $class;
        $this->args      = $args;
        $reflection      = new \ReflectionClass($this->class);
        $this->cache     = $reflection->newInstanceArgs($args);
    }

    /**
     * Magic method to proxy non-overridden calls.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, array $args = array())
    {
        return call_user_func_array(array($this->cache, $method), $args);
    }

    /**
     * @param array $args
     *
     * @return \Doctrine\Bundle\DoctrineCacheBundle\Logger\CacheLoggerProxy
     */
    public function setArgs($args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param \Doctrine\Common\Cache\Cache $cache
     *
     * @return \Doctrine\Bundle\DoctrineCacheBundle\Logger\CacheLoggerProxy
     */
    public function setCache($cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param string $class
     *
     * @return \Doctrine\Bundle\DoctrineCacheBundle\Logger\CacheLoggerProxy
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param \Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster $logMaster
     *
     * @return \Doctrine\Bundle\DoctrineCacheBundle\Logger\CacheLoggerProxy
     */
    public function setLogMaster(LogMaster $logMaster)
    {
        $this->logMaster = $logMaster;

        return $this;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster
     */
    public function getLogMaster()
    {
        return $this->logMaster;
    }

    /**
     * Send a log to the log master.
     *
     * @param string $type
     * @param array  $log
     */
    public function log($type, array $log)
    {
        $this->logMaster->log($this->cacheName, $type, $log);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        $type   = 'fetch';
        $start  = microtime(true);
        $result = $this->cache->fetch($id);
        $end    = microtime(true);
        $this->log($type, array(
            'type'     => $type,
            'start'    => $start,
            'duration' => $end - $start,
            'id'       => $id,
            'data'     => $result,
            'success'  => ($result !== false),
        ));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return $this->cache->contains($id);
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $type   = 'save';
        $start  = microtime(true);
        $result = $this->cache->save($id, $data, $lifeTime);
        $end    = microtime(true);
        $this->log($type, array(
            'type'     => $type,
            'start'    => $start,
            'duration' => $end - $start,
            'id'       => $id,
            'data'     => $data,
            'success'  => $result,
            'lifetime' => $lifeTime,
        ));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $type = 'delete';
        $start = microtime(true);
        $result = $this->cache->delete($id);
        $end = microtime(true);
        $this->log($type, array(
            'type'     => $type,
            'start'    => $start,
            'duration' => $end - $start,
            'id'       => $id,
            'success'  => $result,
        ));

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        return $this->cache->getStats();
    }
}
