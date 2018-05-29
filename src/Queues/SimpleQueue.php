<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ\Queues;

use PhpRSMQ\RedisSMQUtils;
use PhpRSMQ\Messages\MessageInterface;
use PhpRSMQ\Connections\ConnectionInterface;
use PhpRSMQ\Queues\Configs\SimpleQueueConfig;
use PhpRSMQ\Queues\Configs\QueueConfigInterface;
use PhpRSMQ\Queues\Exceptions\QueueException;

/**
 * Simple Queue
 */
class SimpleQueue implements QueueInterface
{
    /**
     * @var ConnectionInterface  $connection  Connection for performing Redis operations.
     * @var QueueConfigInterface $config      Queue config.
     */
    protected $connection, $config;

    /**
     * Constructor sets the connection an synchronizes object with Redis queue (@see SimpleQueue::syncQueue)
     *
     * @param  QueueConfigInterface $config      Queue config.
     * @param  ConnectionInterface  $connection  Connection for performing Redis operations.
     * @throws QueueException                    @see SimpleQueue::syncQueue()
     */
    public function __construct(QueueConfigInterface $config, ConnectionInterface $connection)
    {
        $this->setConnection($connection)
             ->syncQueue($config);
    }

    /**
     * Connection setter
     *
     * @param  ConnectionInterface  $connection  Connection for performing Redis operations.
     */
    protected function setConnection(ConnectionInterface $connection) :SimpleQueue
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Finds the queue in Redis by name from $config and update its attribues (if it is necessary) or create new one. Also sets the config.
     *
     * @param  QueueException  $config  If there is a conneciton problem or problem with creating new queue.
     */
    protected function syncQueue(QueueConfigInterface $config)
    {
        try{
            $hash = $this->connection->hMGet($config->getKey(), array('vt', 'delay', 'maxsize'));
        }catch(RedisException $e){
            throw new QueueException('Error getting queue information due to a redis connection issue!');
        }

        if(in_array(false, $hash, true)){
            $this->createQueue($config);
        }elseif($config->getVt() != $hash['vt'] || $config->getDelay() != $hash['delay'] || $config->getMaxSize() != $hash['maxsize']){
            $this->config = new SimpleQueueConfig($config->getName(), $hash['vt'], $hash['delay'], $hash['maxsize']);
            $this->setQueueAttributes($config);
            return;
        }

        $this->config = $config;
    }

    /**
     * Adds message to queue.
     *
     * @see QueueInterface::sendMessage()
     *
     * @param   MessageInterface    $message  The message to add.
     * @throws  QueueException                If there is a problem with sending the message.
     */
    public function sendMessage(MessageInterface $message) :QueueInterface
    {
        try{
            $time = $this->connection->time();
            $ms   = RedisSMQUtils::formatZeroPad($time[1], 6);
            $ts   = intval($time[0] . substr($ms, 0, 3));
            $id   = base_convert($time[0] . $ms, 10, 36) . RedisSMQUtils::makeid(22);
            $key  = $this->config->getKey();

            if(is_null($delay = $message->getDelay())){
                $delay = $this->config->getDelay();
            }

            $this->connection->multi()
                             ->zAdd($this->config->getName(), $ts + $delay * 1000, $id)
                             ->hSet($key, $id, $message->getMessage())
                             ->hIncrBy($key, 'totalsent', 1)
                             ->exec();
        }catch(ConnectionException $e){
            throw new QueueException('Error sending message due to a redis connection issue!');
        }
        return $this;
    }

    /**
     * Receives message from queue.
     *
     * @see QueueInterface::receiveMessage()
     *
     * @return  MessageInterface     The received message.
     * @throws  QueueException  If there is a problem with receiving the message.
     */
    public function receiveMessage() :MessageInterface
    {
        throw new QueueException('Message receiving is not implemented yet!');
    }

    /**
     * Sets new queue attributes by config.
     *
     * @see QueueInterface::setQueueAttributes()
     *
     * @param   QueueConfigInterface  $config  Config with new attributes.
     * @throws  QueueException                 If there is a problem with updating the attributes.
     */
    public function setQueueAttributes(QueueConfigInterface $config) :QueueInterface
    {
        $key = $this->config->getKey();

        try{
            $this->connection->multi();

            if($this->config->getVt() != $config->getVt()){
                $this->connection->hSet($key, 'vt', $config->getVt());
            }
            if($this->config->getDelay() != $config->getDelay()){
                $this->connection->hSet($key, 'delay', $config->getDelay());
            }
            if($this->config->getMaxSize() != $config->getMaxSize()){
                $this->connection->hSet($key, 'maxsize', $config->getMaxSize());
            }
            if($this->config->getName() != $config->getName()){
                $this->connection->rename($key, $config->getKey());
                $this->connection->rename($this->config->getName(), $config->getName());
                $this->connection->sRem('QUEUES', $this->config->getName());
                $this->connection->sAdd('QUEUES', $config->getName());
            }

            $result = $this->connection->exec();
        }catch(RedisException $e){
            throw new QueueException('Error creating queue due to a redis connection issue!');
        }

        $this->config = $config;
        return $this;
    }

    /**
     * Creates new queue by config.
     *
     * @param  QueueConfigInterface  $config  Config of new queue.
     * @throws ConnectionException            If there is a problem with creating new queue.
     */
    protected function createQueue(QueueConfigInterface $config)
    {
        $key = $config->getKey();
        try{
            $time   = $this->connection->time();
            $result = $this->connection->multi()
                                       ->hSetNx($key, 'vt', $config->getVt())
                                       ->hSetNx($key, 'delay', $config->getDelay())
                                       ->hSetNx($key, 'maxsize', $config->getMaxSize())
                                       ->hSetNx($key, 'created', $time[0])
                                       ->hSetNx($key, 'modified', $time[0])
                                       ->sAdd('QUEUES', $config->getName())
                                       ->exec();
        }catch(RedisException $e){
            throw new QueueException('Error creating queue due to a redis connection issue!');
        }
        if(in_array(0, $result, true)){
            throw new QueueException('Some problem occurred while trying to create new queue "' . $config->getName() . '"!');
        }
    }

    /**
     * Validates message.
     *
     * @param  MessageInterface     $message  Message to validate.
     * @throws ConnectionException            If message is not suitable for this queue.
     */
    public function validateMessage(MessageInterface $message)
    {
        $maxSize = $this->config->getMaxSize();
        if($maxSize != -1 && $maxSize < strlen($message->getMessage())){
            throw new QueueException('Message is too long! Max size is ' . $maxSize . ' characters!');
        }
    }
}
