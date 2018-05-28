<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ\Messages;

/**
 * Message Interface
 */
interface MessageInterface
{
    /**
     * Message setter
     *
     * @param string  $message  String message.
     */
    public function setMessage(string $message) :MessageInterface;

    /**
     * Message getter
     *
     * @param string  $message  String message.
     */
    public function getMessage() :string;

    /**
     * Delay setter
     *
     * Possible values of delay are null (in this case is delay taken from queue config) and number between 0 and 9999999
     *
     * @param  int|null                  $delay  The time in seconds that the delivery of the message will be delayed.
     * @throws InvalidArgumentException
     */
    public function setDelay(int $delay = null) :MessageInterface;

    /**
     * Delay getter
     *
     * @param   int|null  @see MessageInterface::setDelay($delay)
     */
    public function getDelay();
}
