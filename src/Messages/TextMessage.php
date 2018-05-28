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
 * Text Message
 */
class TextMessage extends AbstractMessage
{
    /**
     * @param  string                   $message  String form of message.
     * @param  int|null                 $delay    @see MessageInterface::setDelay($delay)
     * @throws InvalidArgumentException           @see AbstractMessage::setDelay($delay)
     */
    public function __construct(string $message, int $delay = null)
    {
        $this->setMessage($message)
             ->setDelay($delay);
    }

    /**
     * @param  string                   $message  String message.
     * @throws InvalidArgumentException
     */
    public function setMessage(string $message) :MessageInterface
    {
        $message = trim($message);
        if($message == ''){
            throw new InvalidArgumentException('Message is empty!');
        }
        $this->message = $message;
        return $this;
    }

    /**
     * @return string  $message  String message.
     */
    public function getMessage() :string
    {
        return $this->message;
    }
}
