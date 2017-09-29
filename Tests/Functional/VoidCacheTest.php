<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineCacheBundle\Tests\FunctionalTestCase;

/**
 * @group Functional
 * @group Void
 */
class VoidCacheTest extends BaseCacheTest
{
    public function setUp()
    {
        parent::setUp();

        if (!class_exists('Doctrine\Common\Cache\VoidCache')) {
            $this->markTestSkipped('The ' . __CLASS__ .' requires the use of VoidCache available in doctrine/cache since 1.5');
        }
    }

    public function testCacheDriver()
    {
        $cache = $this->createCacheDriver();

        $this->assertNotNull($cache);
        $this->assertInstanceOf('Doctrine\Common\Cache\Cache', $cache);

        $this->assertTrue($cache->save('key', 'value'));
        $this->assertFalse($cache->contains('key'));
        $this->assertFalse($cache->fetch('key'));
        $this->assertTrue($cache->delete('key'));
        $this->assertFalse($cache->contains('key'));
    }

    protected function createCacheDriver()
    {
        $container = $this->compileContainer('void');
        $cache     = $container->get('doctrine_cache.providers.my_void_cache');

        return $cache;
    }
}
