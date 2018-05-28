<?php
namespace PhpRSMQ\Tests\Connections;

use Redis;
use RedisException;
use PHPUnit\Framework\TestCase;
use PhpRSMQ\Connections\RedisProxy;
use PhpRSMQ\Connections\Configs\SimpleConnectionConfig;

class RedisProxyTest extends TestCase
{
    public function setUp()
    {
        $mockRedis = $this->createMock(Redis::class);
        $mockRedis->method('ping')
                  ->willReturn('+PONG');
        $this->mockRedis = $mockRedis;
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructor()
    {
        new RedisProxy($this->mockRedis, new SimpleConnectionConfig('ns'));
    }

    /**
     * @expectedException        PhpRSMQ\Connections\Exceptions\ConnectionException
     * @expectedExceptionMessage Redis connection error!
     */
    public function testConstructorConnectionException()
    {
        $mockRedis = $this->createMock(Redis::class);
        $mockRedis->method('ping')
                  ->will($this->throwException(new RedisException()));
        new RedisProxy($mockRedis, new SimpleConnectionConfig('ns'));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetHandler()
    {

        $connection = new RedisProxy($this->mockRedis, new SimpleConnectionConfig('ns'));

        $mockRedis = $this->createMock(Redis::class);
        $mockRedis->method('ping')
                  ->willReturn('+PONG');

        $connection->setHandler($mockRedis);
    }

    /**
     * @expectedException        PhpRSMQ\Connections\Exceptions\ConnectionException
     * @expectedExceptionMessage Redis connection error!
     */
    public function testSetHandlerConnectionException()
    {
        $connection = new RedisProxy($this->mockRedis, new SimpleConnectionConfig('ns'));

        $mockRedis = $this->createMock(Redis::class);
        $mockRedis->method('ping')
                  ->will($this->throwException(new RedisException()));

        $connection->setHandler($mockRedis);
    }
}
