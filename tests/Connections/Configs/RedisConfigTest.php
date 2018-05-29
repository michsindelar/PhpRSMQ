<?php
namespace PhpRSMQ\Tests\Connections\Configs;

use PHPUnit\Framework\TestCase;
use PhpRSMQ\Connections\Configs\RedisConfig;

class RedisConfigTest extends TestCase
{
    public function testConstructor()
    {
        $config = new RedisConfig();
        $this->assertTrue($config->getPersistence());
        $this->assertSame($config->getHost(), '127.0.0.1');
        $this->assertSame($config->getPort(), 6379);
        $this->assertSame($config->getTimeout(), 0);
        $this->assertSame($config->getRetryInterval(), 100);
        $this->assertSame($config->getReadTimeout(), 0);
        $this->assertNull($config->getPersistentId());

        $config = new RedisConfig(
            true,
            'localhost',
            6800,
            15,
            10,
            100,
            '132456'
        );

        $this->assertTrue($config->getPersistence());
        $this->assertSame($config->getHost(), 'localhost');
        $this->assertSame($config->getPort(), 6800);
        $this->assertSame($config->getTimeout(), 15);
        $this->assertSame($config->getRetryInterval(), 10);
        $this->assertSame($config->getReadTimeout(), 100);
        $this->assertSame($config->getPersistentId(), '132456');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Port is out of range!
     */
    public function testConstructorInvalidArgumentExceptionNegativePort()
    {
        new RedisConfig(
            true,
            'localhost',
            -6379,
            15,
            10,
            100,
            '132456'
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Port is out of range!
     */
    public function testConstructorInvalidArgumentExceptionExtremePort()
    {
        new RedisConfig(
            true,
            'localhost',
            10000000,
            15,
            10,
            100,
            '132456'
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Timeout must be a non-negative number!
     */
    public function testConstructorInvalidArgumentExceptionNegativeTimeout()
    {
        new RedisConfig(
            true,
            'localhost',
            6379,
            -1,
            10,
            100,
            '132456'
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Retry interval must be a non-negative number!
     */
    public function testConstructorInvalidArgumentExceptionNegativeRetryInterval()
    {
        new RedisConfig(
            true,
            'localhost',
            6379,
            15,
            -1,
            100,
            '132456'
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Read timeout must be a non-negative number!
     */
    public function testConstructorInvalidArgumentExceptionNegativeReadTimeout()
    {
        new RedisConfig(
            true,
            'localhost',
            6379,
            15,
            10,
            -1,
            '132456'
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Presistant id can't be set to non-persistant connection!
     */
    public function testConstructorInvalidArgumentExceptionPersistentId()
    {
        new RedisConfig(
            false,
            'localhost',
            6379,
            15,
            10,
            100,
            '132456'
        );
    }
}
