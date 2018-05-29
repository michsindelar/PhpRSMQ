<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ\Queues\Configs;

use InvalidArgumentException;

/**
 * Simple Queue Config
 */
class SimpleQueueConfig implements QueueConfigInterface
{
    /**
     * @var string $name    Queue name.
     * @var string $vt      Visibility time.
     * @var string $delay   Default message delay in seconds.
     * @var string $maxSize Maximal message size in bytes.
     */
    protected $name, $vt, $delay, $maxSize;

    /**
     * @param  string                   $name    @see SimpleQueueConfig::setName()
     * @param  string                   $vt      @see SimpleQueueConfig::setVt() Default: 30
     * @param  string                   $delay   @see SimpleQueueConfig::setDelay() Default: 0
     * @param  string                   $maxSize @see SimpleQueueConfig::setMaxSize() Default: 65536
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, int $vt = 30, int $delay = 0, int $maxSize = 65536)
    {
        $this->setName($name)
             ->setVt($vt)
             ->setDelay($delay)
             ->setMaxSize($maxSize);
    }

    /**
     * Queue key getter.
     *
     * The key is generated from queue name.
     *
     * @return string      The queue key.
     */
    public function getKey() :string
    {
        return $this->name . ':Q';
    }

    /**
     * Queue name setter
     *
     * Maximum 160 characters; alphanumeric characters, hyphens (-), and underscores (_) are allowed.
     *
     * @param  string                   $name  The queue name.
     * @throws InvalidArgumentException
     */
    protected function setName(string $name) :QueueConfigInterface
    {
        if(($name = trim($name)) == ''){
           throw new InvalidArgumentException('The name can\'t be an empty string!');
        }
        if(strlen($name) > 160){
           throw new InvalidArgumentException('The maximum length of the name is 160 characters!');
        }
        if(!preg_match('/^([a-zA-Z0-9-_]+)$/', $name)){
            throw new InvalidArgumentException('Wrong name format! Allowed are alphanumeric characters, hyphens and underscores.');
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Queue name getter.
     *
     * @see QueueConfigInterface::getName()
     */
    public function getName() :string
    {
        return $this->name;
    }

    /**
     * Visibility setter.
     *
     * The length in seconds, that a message received from a queue will be invisible to other receiving components when they ask to receive messages. Allowed values: 0-9999999.
     *
     * @param  int                      $vt
     * @throws InvalidArgumentException
     */
    protected function setVt(int $vt) :QueueConfigInterface
    {
        if($vt < 0 || $vt > 9999999){
            throw new InvalidArgumentException('Visibility is out of range!');
        }
        $this->vt = $vt;
        return $this;
    }

    /**
     * Visibility getter.
     *
     * @see QueueConfigInterface::getVt()
     */
    public function getVt() :int
    {
        return $this->vt;
    }

    /**
     * Delay setter.
     *
     * The time in seconds that the delivery of all new messages in the queue will be delayed. Allowed values: 0-9999999.
     *
     * @param  int                      $delay
     * @throws InvalidArgumentException
     */
    protected function setDelay(int $delay) :QueueConfigInterface
    {
        if($delay < 0 || $delay > 9999999){
            throw new InvalidArgumentException('Delay is out of range!');
        }
        $this->delay = $delay;
        return $this;
    }

    /**
     * Delay getter.
     *
     * @see QueueConfigInterface::getDelay()
     */
    public function getDelay() :int
    {
        return $this->delay;
    }

    /**
     * Max size getter.
     *
     * The maximum message size in bytes. Allowed values: 1024-65536 and -1 (for unlimited size).
     *
     * @param  int                      $maxSize
     * @throws InvalidArgumentException
     */
    protected function setMaxSize(int $maxSize) :QueueConfigInterface
    {
        if($maxSize != -1 && ($maxSize < 1024 || $maxSize > 65536)){
            throw new InvalidArgumentException('Invalid max size!');
        }
        $this->maxSize = $maxSize;
        return $this;
    }

    /**
     * Max size getter
     *
     * @see QueueConfigInterface::getMaxSize()
     */
    public function getMaxSize() :int
    {
        return $this->maxSize;
    }
}
