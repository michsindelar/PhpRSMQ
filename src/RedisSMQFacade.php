<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ;

use Redis;
use InvalidArgumentException;
use PhpRSMQ\Connections\RedisProxy;
use PhpRSMQ\Connections\Configs\RedisConfig;
use PhpRSMQ\Queues\SimpleQueue;
use PhpRSMQ\Queues\Configs\SimpleQueueConfig;
use PhpRSMQ\Messages\TextMessage;
use PhpRSMQ\Messages\JsonMessage;

/**
 * Redis SMQ Fqacade
 *
 * This class provides easy to use api for Redis Simple Message Queue manipulation.
 */
class RedisSMQFacade
{
    /**
     * @var  ConnectionInterface $connection
     * @var  array               $queues     Array of used queue instaces.
     */
    protected $connection, $queues = array();

    /**
     * @param                           $source   Host name of redis server or Redis instance.
     * @param  int|null                 $port     Port of redis server.
     * @throws ConnectionException                @see RedisProxy::__construct()
     * @throws InvalidArgumentException           When a $source has worng format.
     */
    public function __construct($source = null, int $port = null)
    {
        if(is_null($source) || is_string($source)){
            $args = array(true);
            $args[] = $source?? '127.0.0.1';
            if(!is_null($port)){
                $args[] = $port;
            }
            $source = new RedisConfig(...$args);
        }elseif(!($source instanceof Redis)){
            throw new InvalidArgumentException('Wrong type of source argument!');
        }

        $this->connection = new RedisProxy($source);
    }

    /**
     * Sends the message to queue specified by $queue param.
     *
     * If queue is not presented in Redis server this function create one.
     *
     * @param  string                   $queueName  Name of queue.
     * @param  mixed                    $rawMessage String message or message payload that can be encoded to json.
     * @throws InvalidArgumentException             If $queue param is empty string or $message param is not presented.
     * @throws MessageException                     If the message is not string and there is problem encoding.
     * @throws QueueException                       If there is a problem creating a queue or sending a message.
     */
    public function sendMessage(string $queueName, $rawMessage) :RedisSMQFacade
    {
        if(is_null($rawMessage)){
            throw new InvalidArgumentException('Message can\'t be empty!');
        }
        if(isset($this->queues[$queueName])){
            $queue = $this->queues[$queueName];
        }else{
            $queue = new SimpleQueue(new SimpleQueueConfig($queueName), $this->connection);
        }
        if(is_string($rawMessage)){
            $message = new TextMessage($rawMessage);
        }else{
            $message = new JsonMessage($rawMessage);
        }
        $queue->sendMessage($message);
        $this->queues[$queueName] = $queue;
        return $this;
    }
}
