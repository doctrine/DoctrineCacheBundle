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
    /**
     * @var \Doctrine\Common\Cache\ArrayCache
     */
    private $cacheProvider;

    /**
     * @var \Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy
     */
    private $permissionGrantingStrategy;

    /**
     * @var \Doctrine\Bundle\DoctrineCacheBundle\Acl\Model\AclCache
     */
    private $aclCache;

    public function setUp()
    {
        $this->cacheProvider              = new ArrayCache();
        $this->permissionGrantingStrategy = new PermissionGrantingStrategy();
        $this->aclCache                   = new AclCache($this->cacheProvider, $this->permissionGrantingStrategy);
    }

    public function tearDown()
    {
        $this->cacheProvider              = null;
        $this->permissionGrantingStrategy = null;
        $this->aclCache                   = null;
    }

    /**
     * @dataProvider provideDataForEvictFromCacheById
     */
    public function testEvictFromCacheById($expected, $primaryKey)
    {
        $this->cacheProvider->save('bar', 'foo_1');
        $this->cacheProvider->save('foo_1', 's:4:test;');

        $this->aclCache->evictFromCacheById($primaryKey);

        $this->assertEquals($expected, $this->cacheProvider->contains('bar'));
        $this->assertEquals($expected, $this->cacheProvider->contains('foo_1'));
    }

    public function provideDataForEvictFromCacheById()
    {
        return array(
            array(false, 'bar'),
            array(true, 'test'),
        );
    }

    /**
     * @dataProvider provideDataForEvictFromCacheByIdentity
     */
    public function testEvictFromCacheByIdentity($expected, $identity)
    {
        $this->cacheProvider->save('foo_1', 's:4:test;');

        $this->aclCache->evictFromCacheByIdentity($identity);

        $this->assertEquals($expected, $this->cacheProvider->contains('foo_1'));
    }

    public function provideDataForEvictFromCacheByIdentity()
    {
        return array(
            array(false, new ObjectIdentity(1, 'foo')),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPutInCacheWithoutId()
    {
        $acl = new Acl(null, new ObjectIdentity(1, 'foo'), $this->permissionGrantingStrategy, array(), false);

        $this->aclCache->putInCache($acl);
    }

    public function testPutInCacheWithoutParent()
    {
        $acl = $this->getAcl(0);

        $this->aclCache->putInCache($acl);

        $this->assertTrue($this->cacheProvider->contains('foo1_class'));
        $this->assertTrue($this->cacheProvider->contains('oid1'));
    }

    public function testPutInCacheWithParent()
    {
        $acl = $this->getAcl(2);

        $this->aclCache->putInCache($acl);

        // current
        $this->assertTrue($this->cacheProvider->contains('foo2_class'));
        $this->assertTrue($this->cacheProvider->contains('oid2'));

        // parent
        $this->assertTrue($this->cacheProvider->contains('foo3_class'));
        $this->assertTrue($this->cacheProvider->contains('oid3'));

        // grand-parent
        $this->assertTrue($this->cacheProvider->contains('foo4_class'));
        $this->assertTrue($this->cacheProvider->contains('oid4'));
    }

    public function testClearCache()
    {
        $acl = $this->getAcl(0);

        $this->aclCache->putInCache($acl);
        $this->aclCache->clearCache();

        $this->assertFalse($this->cacheProvider->contains('foo5_class'));
        $this->assertFalse($this->cacheProvider->contains('oid5'));
    }

    public function testGetFromCacheById()
    {
        $acl = $this->getAcl(1);

        $this->aclCache->putInCache($acl);

        $cachedAcl = $this->aclCache->getFromCacheById($acl->getId());

        $this->assertEquals($acl->getId(), $cachedAcl->getId());
        $this->assertNotNull($cachedParentAcl = $cachedAcl->getParentAcl());
        $this->assertEquals($acl->getParentAcl()->getId(), $cachedParentAcl->getId());

        $this->assertEquals($acl->getClassFieldAces('foo'), $cachedAcl->getClassFieldAces('foo'));
        $this->assertEquals($acl->getObjectFieldAces('foo'), $cachedAcl->getObjectFieldAces('foo'));
    }

    public function testGetFromCacheByIdentity()
    {
        $acl = $this->getAcl(1);

        $this->aclCache->putInCache($acl);

        $cachedAcl = $this->aclCache->getFromCacheByIdentity($acl->getObjectIdentity());

        $this->assertEquals($acl->getId(), $cachedAcl->getId());
        $this->assertNotNull($cachedParentAcl = $cachedAcl->getParentAcl());
        $this->assertEquals($acl->getParentAcl()->getId(), $cachedParentAcl->getId());

        $this->assertEquals($acl->getClassFieldAces('foo'), $cachedAcl->getClassFieldAces('foo'));
        $this->assertEquals($acl->getObjectFieldAces('foo'), $cachedAcl->getObjectFieldAces('foo'));
    }

    protected function getAcl($depth = 0)
    {
        static $id = 1;

        $acl = new Acl(
            'oid' . $id,
            new ObjectIdentity('class', 'foo' . $id),
            $this->permissionGrantingStrategy,
            array(),
            $depth > 0
        );

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
}
