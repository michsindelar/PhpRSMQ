<?php
namespace PhpRSMQ\Tests\Messages;

use PHPUnit\Framework\TestCase;
use PhpRSMQ\Messages\TextMessage;

class TextMessageTest extends TestCase
{
    public function setUp()
    {
        $this->message = new TextMessage('Brand new message!');
    }

    public function testConstructor()
    {
        $message = new TextMessage('Brand new message!');
        $this->assertSame($message->getMessage(), 'Brand new message!');
        $this->assertSame($message->getDelay(), null);

        $message = new TextMessage('Brand new message!', 100);
        $this->assertSame($message->getMessage(), 'Brand new message!');
        $this->assertSame($message->getDelay(), 100);
    }


    public function testSetMessage()
    {
        $this->message->setMessage('   Brand new message!    ');
        $this->assertSame($this->message->getMessage(), 'Brand new message!');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Message is empty!
     */
    public function testSetMessageMessageException()
    {
        $this->message->setMessage('  ');
    }
}
