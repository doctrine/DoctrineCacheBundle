<?php

namespace Doctrine\Bundle\DoctrineCacheBundle\Tests\Helper;

use Doctrine\Bundle\DoctrineCacheBundle\Helper\RedisUrlParser;
use Doctrine\Bundle\DoctrineCacheBundle\Tests\TestCase;

class UrlParserTest extends TestCase
{
    public function testParseUrl()
    {
        $config = RedisUrlParser::parse(['url' => 'redis://1.2.3.4']);

        $this->assertArrayHasKey('host', $config);
        $this->assertArrayNotHasKey('port', $config);
        $this->assertArrayNotHasKey('password', $config);
        $this->assertArrayNotHasKey('database', $config);
        $this->assertSame('1.2.3.4', $config['host']);
    }

    public function testParseUrlWithPort()
    {
        $config = RedisUrlParser::parse(['url' => 'redis://1.2.3.4:6379']);

        $this->assertArrayHasKey('host', $config);
        $this->assertArrayHasKey('port', $config);
        $this->assertArrayNotHasKey('password', $config);
        $this->assertArrayNotHasKey('database', $config);
        $this->assertSame('1.2.3.4', $config['host']);
        $this->assertSame('6379', $config['port']);
    }

    public function testParseUrlWithPassword()
    {
        $config = RedisUrlParser::parse(['url' => 'redis://p4ssw0rd@1.2.3.4']);

        $this->assertArrayHasKey('host', $config);
        $this->assertArrayNotHasKey('port', $config);
        $this->assertArrayHasKey('password', $config);
        $this->assertArrayNotHasKey('database', $config);
        $this->assertSame('1.2.3.4', $config['host']);
        $this->assertSame('p4ssw0rd', $config['password']);
    }

    public function testParseUrlWithDatabase()
    {
        $config = RedisUrlParser::parse(['url' => 'redis://1.2.3.4/0']);

        $this->assertArrayHasKey('host', $config);
        $this->assertArrayNotHasKey('port', $config);
        $this->assertArrayNotHasKey('password', $config);
        $this->assertArrayHasKey('database', $config);
        $this->assertSame('1.2.3.4', $config['host']);
        $this->assertSame('0', $config['database']);
    }

    public function testParseUrlFull()
    {
        $config = RedisUrlParser::parse(['url' => 'redis://p4ssw0rd@1.2.3.4:6379/0']);

        $this->assertArrayHasKey('host', $config);
        $this->assertArrayHasKey('port', $config);
        $this->assertArrayHasKey('password', $config);
        $this->assertArrayHasKey('database', $config);
        $this->assertSame('1.2.3.4', $config['host']);
        $this->assertSame('6379', $config['port']);
        $this->assertSame('p4ssw0rd', $config['password']);
        $this->assertSame('0', $config['database']);
    }

    public function testParseUrlPartialOverride()
    {
        $config = RedisUrlParser::parse(
            [
                'url' => 'redis://url_password@host',
                'port' => '6379',
                'password' => 'explicit_password',
            ]
        );

        $this->assertSame('url_password', $config['password']);
        $this->assertSame('6379', $config['port']);
    }

    public function testParseUrlFullOverride()
    {
        $config = RedisUrlParser::parse(
            [
                'url' => 'redis://url_password@url_host:9736',
                'host' => 'explicit_host',
                'port' => '6379',
                'password' => 'explicit_password',
                'database' => '1',
            ]
        );

        $this->assertSame('url_password', $config['password']);
        $this->assertSame('url_host', $config['host']);
        $this->assertSame('9736', $config['port']);
        $this->assertSame('1', $config['database']);
    }

    public function testParseUrlWithoutUrlDoesNothing()
    {
        $config = ['something' => 'else'];

        $this->assertSame($config, RedisUrlParser::parse($config));
    }

    public function testParseUrlPasswordWithoutEncoding()
    {
        $config = RedisUrlParser::parse(['url' => 'redis://VIfb2^$\zA@host']);
        $this->assertSame('VIfb2^$\zA', $config['password']);
    }

    public function testParseUrlPasswordWithPercentEncoding()
    {
        $config = RedisUrlParser::parse(['url' => 'redis://foobar%2F@host']);
        $this->assertSame('foobar/', $config['password']);
    }

    public function testParseUrlPasswordWithPercentSign()
    {
        $config = RedisUrlParser::parse(['url' => 'redis://foo%25bar@host']);
        $this->assertSame('foo%bar', $config['password']);
    }
}
