<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ\Queues;

use PhpRSMQ\Messages\MessageInterface;
use PhpRSMQ\Queues\Configs\QueueConfigInterface;

/**
 * Queue Interface
 */
interface QueueInterface
{
  /**
   * Adds message to queue.
   *
   * @param   MessageInterface    $message  The message to add.
   * @throws  ConnectionException
   */
    public function sendMessage(MessageInterface $message) :QueueInterface;

    /**
     * Receives message from queue.
     *
     * @return  MessageInterface    The received message.
     * @throws  ConnectionException
     */
    public function receiveMessage() :MessageInterface;

    /**
     * Sets new queue attributes by config.
     *
     * @param   QueueConfigInterface  $config  Config with new attributes.
     * @throws  QueueException
     */
    public function setQueueAttributes(QueueConfigInterface $config) :QueueInterface;
}
