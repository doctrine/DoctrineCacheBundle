<?php


namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Fixtures;

class Memcached extends \Memcached
{
    protected $persistentId;

    public function __construct($persistent_id = null, $callback = null)
    {
        parent::__construct($persistent_id, $callback);
        $this->persistentId = $persistent_id;
    }

    public function getPersistentId()
    {
        return $this->persistentId;
    }
}
