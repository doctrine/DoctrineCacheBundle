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

    public function testParseUrlOverride()
    {
        $config = RedisUrlParser::parse(
            [
                'url' => 'redis://url_password@host',
                'password' => 'explicit_password',
            ]
        );

        $this->assertSame('url_password', $config['password']);
    }

    public function testParseUrlWithoutUrlDoesNothing()
    {
        $config = ['something' => 'else'];

        $this->assertSame($config, RedisUrlParser::parse($config));
    }
}
