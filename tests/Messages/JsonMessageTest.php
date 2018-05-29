<?php
namespace PhpRSMQ\Tests\Messages;

use PHPUnit\Framework\TestCase;
use PhpRSMQ\Messages\JsonMessage;

class JsonMessageTest extends TestCase
{
    public function setUp()
    {
        $this->message = new JsonMessage(array('msg' => 'Brand new message!'));
    }

    public function testConstructor()
    {
        $this->assertSame($this->message->getPayload(), array('msg' => 'Brand new message!'));
        $this->assertSame($this->message->getMessage(), json_encode(array('msg' => 'Brand new message!')));
        $this->assertSame($this->message->getDelay(), null);

        $message = new JsonMessage(json_encode(array('msg' => 'Brand new message!')), 100);
        $this->assertSame($this->message->getPayload(), array('msg' => 'Brand new message!'));
        $this->assertSame($message->getMessage(), json_encode(array('msg' => 'Brand new message!')));
        $this->assertSame($message->getDelay(), 100);
    }

    public function testSetPayload()
    {
        $this->message->setPayload(array('msg' => 'Second brand new message!'));
        $this->assertSame($this->message->getPayload(), array('msg' => 'Second brand new message!'));
        $this->assertSame($this->message->getMessage(), json_encode(array('msg' => 'Second brand new message!')));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Payload is empty!
     */
    public function testSetPayloadInvalidArgumentExceptionEmptyPayload()
    {
        $this->message->setPayload(array());
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Message json encoding error!
     */
    public function testSetPayloadInvalidArgumentExceptionWrongPayload()
    {
        $this->message->setPayload("\xB1\x31");
    }

    public function testGetPayload()
    {
        $this->assertEquals($this->message->getPayload(false), (object) array('msg' => 'Brand new message!'));
    }

    public function testSetMessage()
    {
        $this->message->setMessage(json_encode(array('msg' => 'Second brand new message!')));
        $this->assertSame($this->message->getPayload(), array('msg' => 'Second brand new message!'));
        $this->assertSame($this->message->getMessage(), json_encode(array('msg' => 'Second brand new message!')));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Message is empty!
     */
    public function testSetMessageInvalidArgumentExceptionEmptyMessage()
    {
        $this->message->setMessage(json_encode(array()));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Message json decoding error!
     */
    public function testSetMessageInvalidArgumentExceptionWrongMessage()
    {
        $this->message->setMessage('Brand new message!');
    }
}
