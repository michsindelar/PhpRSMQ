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
 * Abstract Message
 */
abstract class AbstractMessage implements MessageInterface
{
    /**
     * @var string    $message  String form of message.
     * @var int|null  $delay    @see MessageInterface::setDelay()
     */
    protected $message, $delay;

    /**
     * @param string  $message  String form of message.
     */
    abstract public function setMessage(string $message) :MessageInterface;

    /**
     * @return string  $message  String form of message.
     */
    abstract public function getMessage() :string;

    /**
     * @param   int|null  $delay  @see MessageInterface::setDelay()
     * @throws  InvalidArgumentException
     */
    public function setDelay(int $delay = null) :MessageInterface
    {
        if(!is_null($delay) && ($delay<0 || $delay>9999999)){
            throw new InvalidArgumentException('Incorrect delay value! Posible values are null and number between 0 and 9999999.');
        }
        $this->delay = $delay;
        return $this;
    }

    /**
     * @return int|null  $delay  @see MessageInterface::setDelay()
     */
    public function getDelay()
    {
        return $this->delay;
    }
}
