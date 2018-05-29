<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ\Connections\Configs;

use InvalidArgumentException;

/**
 * Redis Config
 */
class RedisConfig
{
    /**
     * @var bool        $persistent      If connection should be persistant.
     * @var string      $host
     * @var int         $port
     * @var int         $timeout
     * @var int         $retryInterval
     * @var int         $readTimeout
     * @var string|null $persistentId    Id of established connection (only if connection is persistant).
     */
    protected $persistent, $host, $port, $timeout, $retryInterval, $readTimeout, $persistentId;

    /**
     * @param bool        $persistent      If connection should be persistant. Default: true
     * @param string      $host            Default: '127.0.0.1'
     * @param int         $port            Default: 6379
     * @param int         $timeout         Default: 0
     * @param int         $retryInterval   Default: 100
     * @param int         $readTimeout     Default: 0
     * @param string|null $persistentId    Id of established connection (only if connection is persistant). Default: null
     */
    public function __construct(bool $persistent = true, string $host = '127.0.0.1', int $port = 6379, int $timeout = 0, int $retryInterval = 100, int $readTimeout = 0, string $persistentId = null){
        $this->setPersistence($persistent)
             ->setPort($port)
             ->setHost($host)
             ->setTimeout($timeout)
             ->setRetryInterval($retryInterval)
             ->setReadTimeout($readTimeout)
             ->setPersistentId($persistentId);
    }

    /**
     * @param   bool                      $persistent If connection should be persistant.
     * @throws  InvalidArgumentException              If presistant id was specified.
     */
    protected function setPersistence(bool $persistent)
    {
        if(!$persistent && !is_null($this->persistentId)){
            throw new InvalidArgumentException('Persistance can\'t be set with presistant id!');
        }
        $this->persistent = $persistent;
        return $this;
    }

    /**
     * @return  bool         If connection should be persistant
     */
    public function getPersistence() :bool
    {
        return $this->persistent;
    }

    /**
     * @param  string  $host  Host name or Ip address
     */
    protected function setHost(string $host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return  string  Host name or Ip address
     */
    public function getHost() :string
    {
        return $this->host;
    }

    /**
     * @param  int                      $port
     * @throws InvalidArgumentException        If port is out of range
     */
    protected function setPort(int $port)
    {
        if($port < 0 || $port > 65535){
            throw new InvalidArgumentException('Port is out of range!');
        }
        $this->port = $port;
        return $this;
    }

    /**
     * @return int  Port
     */
    public function getPort() :int
    {
        return $this->port;
    }

    /**
     * @param  int                      $timeout
     * @throws InvalidArgumentException          If timeout is negative number.
     */
    protected function setTimeout(int $timeout)
    {
        if($timeout < 0){
            throw new InvalidArgumentException('Timeout must be a non-negative number!');
        }
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @return int  Timeout
     */
    public function getTimeout() :int
    {
        return $this->timeout;
    }

    /**
     * @param  int                      $retryInterval
     * @throws InvalidArgumentException                 If retry interval is negative number.
     */
    protected function setRetryInterval(int $retryInterval)
    {
        if($retryInterval < 0){
            throw new InvalidArgumentException('Retry interval must be a non-negative number!');
        }
        $this->retryInterval = $retryInterval;
        return $this;
    }

    /**
     * @return int  Retry interval
     */
    public function getRetryInterval() :int
    {
        return $this->retryInterval;
    }

    /**
     * @param  int                      $readTimeout
     * @throws InvalidArgumentException              If read timeout is negative number.
     */
    protected function setReadTimeout(int $readTimeout)
    {
        if($readTimeout < 0){
            throw new InvalidArgumentException('Read timeout must be a non-negative number!');
        }
        $this->readTimeout = $readTimeout;
        return $this;
    }

    /**
     * @return int Read timeout
     */
    public function getReadTimeout() :int
    {
        return $this->readTimeout;
    }

    /**
     * @param  string|null              $persistentId  Id of established connection (only if connection is persistant).
     * @throws InvalidArgumentException                If connection is not persistant and id is string.
     */
    protected function setPersistentId(string $persistentId = null)
    {
        if(!$this->persistent && !is_null($persistentId)){
            throw new InvalidArgumentException('Presistant id can\'t be set to non-persistant connection!');
        }
        $this->persistentId = $persistentId;
        return $this;
    }

    /**
     * @return string|null Persistent id
     */
    public function getPersistentId()
    {
        return $this->persistentId;
    }
}
