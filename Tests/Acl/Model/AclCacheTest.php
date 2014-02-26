<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Acl\Domain;

use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;
use Symfony\Component\Security\Acl\Domain\Acl;
use Doctrine\Bundle\DoctrineCacheBundle\Acl\Model\AclCache;
use Doctrine\Common\Cache\ArrayCache;

class AclCacheTest extends \PHPUnit_Framework_TestCase
{
    protected $permissionGrantingStrategy;

    public function test()
    {
        $cache = $this->getAclCache();

        $aclWithParent = $this->getAcl(1);
        $acl = $this->getAcl();

        $cache->putInCache($aclWithParent);
        $cache->putInCache($acl);

        $cachedAcl = $cache->getFromCacheByIdentity($acl->getObjectIdentity());

        $this->assertEquals($acl->getId(), $cachedAcl->getId());
        $this->assertNull($acl->getParentAcl());

        $cachedAclWithParent = $cache->getFromCacheByIdentity($aclWithParent->getObjectIdentity());

        $this->assertEquals($aclWithParent->getId(), $cachedAclWithParent->getId());
        $this->assertNotNull($cachedParentAcl = $cachedAclWithParent->getParentAcl());
        $this->assertEquals($aclWithParent->getParentAcl()->getId(), $cachedParentAcl->getId());
    }

    protected function getAcl($depth = 0)
    {
        static $id = 1;

        $acl = new Acl($id, new ObjectIdentity($id, 'foo'), $this->getPermissionGrantingStrategy(), array(), $depth > 0);

        // insert some ACEs
        $sid = new UserSecurityIdentity('johannes', 'Foo');

        $acl->insertClassAce($sid, 1);
        $acl->insertClassFieldAce('foo', $sid, 1);
        $acl->insertObjectAce($sid, 1);
        $acl->insertObjectFieldAce('foo', $sid, 1);

        $id++;

        if ($depth > 0) {
            $acl->setParentAcl($this->getAcl($depth - 1));
        }

        return $acl;
    }

    protected function getPermissionGrantingStrategy()
    {
        if (null === $this->permissionGrantingStrategy) {
            $this->permissionGrantingStrategy = new PermissionGrantingStrategy();
        }

        return $this->permissionGrantingStrategy;
    }

    protected function getAclCache($cacheDriver = null)
    {
        if (null === $cacheDriver) {
            $cacheDriver = new ArrayCache();
        }

        return new AclCache($cacheDriver, $this->getPermissionGrantingStrategy());
    }
}
