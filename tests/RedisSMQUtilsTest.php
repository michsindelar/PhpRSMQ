<?php
namespace PhpRSMQ\Tests;

use PHPUnit\Framework\TestCase;
use PhpRSMQ\RedisSMQUtils;

class RedisSMQUtilsTest extends TestCase
{
    public function testMakeId()
    {
        $this->assertSame(
            strlen(RedisSMQUtils::makeId(10)),
            10
        );

        $this->assertSame(
            RedisSMQUtils::makeId(0),
            ''
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Lenght must be a non-negative number
     */
    public function testMakeIdInvalidArgumentException()
    {
        RedisSMQUtils::makeId(-10);
    }

    public function testFormatZeroPad()
    {
        $this->assertSame(
            RedisSMQUtils::formatZeroPad(1234, 15),
            '000000000001234'
        );

        $this->assertSame(
            RedisSMQUtils::formatZeroPad(1234, 2),
            '1234'
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Arguments must be non-negative numbers!
     */
    public function testFormatZeroPadInvalidArgumentExceptionNegativeNum()
    {
        RedisSMQUtils::formatZeroPad(-1, 1);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Arguments must be non-negative numbers!
     */
    public function testFormatZeroPadInvalidArgumentExceptionNegativeCount()
    {
        RedisSMQUtils::formatZeroPad(1, -1);
    }
}
