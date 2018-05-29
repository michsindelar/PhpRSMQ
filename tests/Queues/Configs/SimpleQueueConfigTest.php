<?php
namespace PhpRSMQ\Tests\Queues\Configs;

use PHPUnit\Framework\TestCase;
use PhpRSMQ\Queues\Configs\SimpleQueueConfig;

class SimpleQueueConfigTest extends TestCase
{
    public function testConstructor()
    {
        $config = new SimpleQueueConfig('queue');
        $this->assertSame($config->getKey(), 'queue:Q');
        $this->assertSame($config->getName(), 'queue');
        $this->assertSame($config->getVt(), 30);
        $this->assertSame($config->getDelay(), 0);
        $this->assertSame($config->getMaxSize(), 65536);

        $config = new SimpleQueueConfig('  queue  ', 10, 5, 1024);
        $this->assertSame($config->getKey(), 'queue:Q');
        $this->assertSame($config->getName(), 'queue');
        $this->assertSame($config->getVt(), 10);
        $this->assertSame($config->getDelay(), 5);
        $this->assertSame($config->getMaxSize(), 1024);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The name can't be an empty string!
     */
    public function testConstructorInvalidArgumentExceptionEmptyName()
    {
        new SimpleQueueConfig('');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The maximum length of the name is 160 characters!
     */
    public function testConstructorInvalidArgumentExceptionLongName()
    {
        new SimpleQueueConfig('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Wrong name format! Allowed are alphanumeric characters, hyphens and underscores.
     */
    public function testConstructorInvalidArgumentExceptionWrongName()
    {
        new SimpleQueueConfig('#');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Visibility is out of range!
     */
    public function testConstructorInvalidArgumentExceptionNegativeVisibility()
    {
        new SimpleQueueConfig('queue', -1);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Visibility is out of range!
     */
    public function testConstructorInvalidArgumentExceptionExtremeVisibility()
    {
        new SimpleQueueConfig('queue', 10000000);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Delay is out of range!
     */
    public function testConstructorInvalidArgumentExceptionNegativeDelay()
    {
        new SimpleQueueConfig('queue', 30, -1);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Delay is out of range!
     */
    public function testConstructorInvalidArgumentExceptionExtremeDelay()
    {
        new SimpleQueueConfig('queue', 30, 10000000);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid max size!
     */
    public function testConstructorInvalidArgumentExceptionInvalidMaxSize()
    {
        new SimpleQueueConfig('queue', 30, 0, -2);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid max size!
     */
    public function testConstructorInvalidArgumentExceptionLowMaxSize()
    {
        new SimpleQueueConfig('queue', 30, 0, 1023);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Invalid max size!
     */
    public function testConstructorInvalidArgumentExceptionExtremeMaxSize()
    {
        new SimpleQueueConfig('queue', 30, 0, 65537);
    }
}
