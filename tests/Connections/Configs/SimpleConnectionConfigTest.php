<?php
namespace PhpRSMQ\Tests\Connections\Configs;

use PHPUnit\Framework\TestCase;
use PhpRSMQ\Connections\Configs\SimpleConnectionConfig;

class SimpleConnectionConfigTest extends TestCase
{
    public function testConstructor()
    {
        $this->assertSame((new SimpleConnectionConfig())->getNs(), 'rsmq');
        $this->assertSame((new SimpleConnectionConfig('abc'))->getNs(), 'abc');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Wrong namespace format!
     */
    public function testConstructorInvalidArgumentExceptionNs()
    {
        new SimpleConnectionConfig('*');
    }
}
