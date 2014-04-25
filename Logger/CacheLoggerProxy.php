<?php
/**
 *
 */

namespace Doctrine\Bundle\DoctrineCacheBundle\Logger;

use Doctrine\Bundle\DoctrineCacheBundle\Logger\LogMaster;
use Doctrine\Common\Cache\Cache;

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

    public function __construct($serviceId, $class, $args)
    {
        $this->serviceId = $serviceId;
        $this->cacheName = substr($serviceId, strrpos($serviceId, '.') + 1);
        $this->class     = $class;
        $this->args      = $args;
        $reflection      = new \ReflectionClass($this->class);
        $this->cache     = $reflection->newInstanceArgs($args);
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->cache, $method), $args);
    }

    /**
     * @param array $args
     * @return CacheLoggerProxy
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
     * @return CacheLoggerProxy
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
     * @return CacheLoggerProxy
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
     * @return CacheLoggerProxy
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

    public function log($type, $log)
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
            'type'      => $type,
            'timestamp' => time(),
            'duration'  => $end - $start,
            'request'   => $id,
            'result'    => $result,
        ));

        return $result;
    }

    function contains($id)
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
            'type' => $type,
            'timestamp' => time(),
            'duration' => $end - $start,
            'request' => array(
                'id'       => $id,
                'data'     => $data,
                'lifetime' => $lifeTime,
            ),
            'result' => $result,
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
            'type'      => $type,
            'timestamp' => time(),
            'duration'  => $end - $start,
            'request'   => $id,
            'result'    => $result,
        ));

        return $result;
    }

    function getStats()
    {
        return $this->cache->getStats();
    }
}
