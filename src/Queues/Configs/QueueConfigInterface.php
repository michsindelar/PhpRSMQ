<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ\Queues\Configs;

/**
 * Queue Config Interface
 */
interface QueueConfigInterface
{
    /**
     * Queue key getter.
     *
     * @return string      The queue key.
     */
    public function getKey() :string;

    /**
     * Queue name getter.
     *
     * The Queue name is maximum 160 characters; alphanumeric characters, hyphens (-), and underscores (_) are allowed.
     *
     * @return string Queue name.
     */
    public function getName() :string;

    /**
     * Visibility getter.
     *
     * The length in seconds, that a message received from a queue will be invisible to other receiving components when they ask to receive messages. Allowed values: 0-9999999.
     *
     * @return int
     */
    public function getVt() :int;

    /**
     * Delay getter
     *
     * The time in seconds that the delivery of all new messages in the queue will be delayed. Allowed values: 0-9999999.
     *
     * @return int      Delay in seconds.
     */
    public function getDelay() :int;

    /**
     * Max size getter
     *
     * The maximum message size in bytes. Allowed values: 1024-65536 and -1 (for unlimited size)
     *
     * @return int     Maximal length
     */
    public function getMaxSize() :int;
}
