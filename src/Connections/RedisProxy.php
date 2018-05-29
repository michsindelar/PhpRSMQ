<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ\Connections;

use Redis;
use RedisException;
use InvalidArgumentException;
use PhpRSMQ\Connections\Configs\ConnectionConfigInterface;
use PhpRSMQ\Connections\Configs\RedisConfig;
use PhpRSMQ\Connections\Configs\SimpleConnectionConfig;
use PhpRSMQ\Connections\Exceptions\ConnectionException;

/**
 * Redis Connection Proxy
 */
class RedisProxy implements ConnectionInterface
{
    /**
     * @var  Redis                      $handler  Redis connection handler.
     * @var  ConnectionConfigInterface  $config   Connection config.
     */
    protected $handler, $config;

    /**
     * @param   Redis|RedisConfig          $source   Source of connecton.
     * @param   ConnectionConfigInterface  $config   Connection config (optional).
     * @throws  ConnectionException
     */
    public function __construct($source, ConnectionConfigInterface $config = null)
    {
        if($source instanceof Redis){
            $handler = $source;
        }elseif($source instanceof RedisConfig){
            $handler  = new Redis();
            $function = $source->getPersistence()? 'pconnect': 'connect';
            $handler->$function(
                $source->getHost(),
                $source->getPort(),
                $source->getTimeout(),
                $source->getPersistentId(),
                $source->getRetryInterval(),
                $source->getReadTimeout()
            );
        }else{
            throw new InvalidArgumentException('Wrong source type!');
        }

        $this->setHandler($handler);
        if(is_null($config)){
            $config = new SimpleConnectionConfig();
        }
        $this->setConfig($config);
    }

    /**
     * @throws  ConnectionException   If there is a connecion problem.
     */
    public function checkConnection()
    {
        try{
            $this->handler->ping();
        }catch(RedisException $e){
            throw new ConnectionException('Redis connection error!');
        }
    }

    /**
     * Handler setter.
     *
     * @var     Redis                $handler  Redis connection handler.
     * @throws  ConnectionException            If there is a connecion problem.
     */
    public function setHandler(Redis $handler) :RedisProxy
    {
        $this->handler = $handler;
        $this->checkConnection();
        return $this;
    }

    /**
     * Config setter.
     *
     * @throws  ConnectionConfigInterface  $config   Connection config.
     */
    protected function setConfig(ConnectionConfigInterface $config) :RedisProxy
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Extends key by connection namespace.
     *
     * @param  string  $key  Key to prepare.
     * @return string        Correct form of the key for this connection.
     */
    protected function prepareKey(string $key) :string
    {
        return $this->config->getNs() . ':' . $key;
    }

    /**
     * Namespace getter.
     *
     * @return string        Namespace for this connection.
     */
    public function getNs() :string
    {
        return $this->config->getNs();
    }

    /**
     * Call redis proxied method.
     *
     * @param  string            $method  Name of the proxied method.
     * @param  array             $args    Array of argements for proxied method.
     * @return mixed|RedisProxy           Result of operation or self if operation is in transaction.
     */
    protected function callHandlerMethod(string $method, array $args = array())
    {
        try{
            $result = call_user_func_array(array($this->handler, $method), $args);
            if($result === $this->handler)
              return $this;
            else
              return $result;
        }catch(RedisException $e){
            throw new ConnectionException('Redis connection error!');
        }
    }

    /**
     * Starts trasaction.
     *
     * @see ConnectionInterface::multi()
     */
    public function multi()  :ConnectionInterface
    {
        try{
            $this->handler->multi();
            return $this;
        }catch(RedisException $e){
            throw new ConnectionException('Redis connection error!');
        }
    }

    /**
     * Executes commands in transaction.
     *
     * @see ConnectionInterface::exec()
     */
    public function exec()
    {
        try{
            return $this->handler->exec();
        }catch(RedisException $e){
            throw new ConnectionException('Redis connection error!');
        }
    }

    /**
     * Redis time getter.
     *
     * @see ConnectionInterface::time()
     */
    public function time()
    {
        return $this->callHandlerMethod('time');
    }

    /**
     * Redis key rename.
     *
     * @see ConnectionInterface::rename()
     */
    public function rename(string $oldKey, string $newKey)
    {
        return $this->callHandlerMethod('rename', array($this->prepareKey($oldKey), $this->prepareKey($newKey)));
    }

    /**
     * Hash fields getter.
     *
     * @see ConnectionInterface::hMGet()
     */
    public function hMGet(string $key, array $hashKeys)
    {
        return $this->callHandlerMethod('hMGet', array($this->prepareKey($key), $hashKeys));
    }

    /**
     * Adds a value to the set.
     *
     * @see ConnectionInterface::sAdd()
     */
    public function sAdd(string $key, string $value)
    {
        return $this->callHandlerMethod('sAdd', array($this->prepareKey($key), $value));
    }

    /**
     * Removes a value from the set.
     *
     * @see ConnectionInterface::sRem()
     */
    public function sRem(string $key, string $value)
    {
        return $this->callHandlerMethod('sRem', array($this->prepareKey($key), $value));
    }

    /**
     * Adds value to a sorted set or update its score if it already exists.
     *
     * @see ConnectionInterface::zAdd()
     */
    public function zAdd(string $key, float $score, string $value)
    {
        return $this->callHandlerMethod('zAdd', array($this->prepareKey($key), $score, $value));
    }

    /**
     * Adds a value to the hash stored at key.
     *
     * @see ConnectionInterface::hSet()
     */
    public function hSet(string $key, string $hashKey, string $value)
    {
        return $this->callHandlerMethod('hSet', array($this->prepareKey($key), $hashKey, $value));
    }

    /**
     * Adds a value to the hash stored at key (only if this field isn't already in the hash).
     *
     * @see ConnectionInterface::hSetNx()
     */
    public function hSetNx(string $key, string $hashKey, string $value)
    {
        return $this->callHandlerMethod('hSetNx', array($this->prepareKey($key), $hashKey, $value));
    }

    /**
     * Increments the value of a hash key from a hash by a given amount.
     *
     * @see ConnectionInterface::hIncrBy()
     */
    public function hIncrBy(string $key, string $value, int $amount)
    {
        return $this->callHandlerMethod('hIncrBy', array($this->prepareKey($key), $value, $amount));
    }
}
