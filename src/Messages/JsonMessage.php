<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ\Messages;

use InvalidArgumentException;

/**
 * Json Message
 */
class JsonMessage extends AbstractMessage
{
    /**
     * @param  mixed                    $payload   Payload which can be encoded to string.
     * @param  int                      $delay     @see MessageInterface::setDelay()
     * @throws InvalidArgumentException            @see AbstractMessage::setDelay()
     */
    public function __construct($payload, int $delay = null)
    {
        if(is_string($payload)){
            $this->setMessage($payload);
        }else{
            $this->setPayload($payload);
        }
        $this->setDelay($delay);
    }

    /**
     * @param  mixed                    $payload  Payload which can be encoded to string.
     * @throws InvalidArgumentException           If the message payload is empty or if there is a problem with encoding.
     */
    public function setPayload($payload)
    {
        if(empty($payload)){
            throw new InvalidArgumentException('Payload is empty!');
        }
        $message = json_encode($payload);
        if($message == false){
            throw new InvalidArgumentException('Message json encoding error!');
        }
        $this->message = $message;
        return $this;
    }

    /**
     * @param   bool   $assoc  If returned object should be converted to associative array.
     * @return  mixed          Json payload of message.
     */
    public function getPayload($assoc = true)
    {
        return json_decode($this->message, $assoc);
    }

    /**
     * @param  string                   $message Json encoded message.
     * @throws InvalidArgumentException          If the message is empty or if there is a problem with decoding.
     */
    public function setMessage(string $message) :MessageInterface
    {
        $message = trim($message);
        $payload = json_decode($message);
        if(is_null($payload)){
            throw new InvalidArgumentException('Message json decoding error!');
        }
        if(empty($payload)){
            throw new InvalidArgumentException('Message is empty!');
        }
        $this->message = $message;
        return $this;
    }

    /**
     * @return  string  String form of message.
     */
    public function getMessage() :string
    {
        return $this->message;
    }
}
