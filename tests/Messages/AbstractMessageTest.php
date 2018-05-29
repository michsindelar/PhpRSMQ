<?php
namespace PhpRSMQ\Tests\Messages;

use PHPUnit\Framework\TestCase;
use PhpRSMQ\Messages\AbstractMessage;

class AbstractMessageTest extends TestCase
{
    public function setUp()
    {
        $this->mockMessage = $this->getMockForAbstractClass(AbstractMessage::class);
    }

    public function testSetDelay()
    {
        $this->mockMessage->setDelay(300);
        $this->assertSame($this->mockMessage->getDelay(), 300);

        $this->mockMessage->setDelay(0);
        $this->assertSame($this->mockMessage->getDelay(), 0);

        $this->mockMessage->setDelay(null);
        $this->assertNull($this->mockMessage->getDelay());
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Incorrect delay value! Posible values are null and number between 0 and 9999999.
     */
    public function testSetDelayInvalidArgumentExceptionNegativeDelay()
    {
        $this->mockMessage->setDelay(-1);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Incorrect delay value! Posible values are null and number between 0 and 9999999.
     */
    public function testSetDelayInvalidArgumentExceptionExtremeDelay()
    {
        $this->mockMessage->setDelay(10000000);
    }
}
