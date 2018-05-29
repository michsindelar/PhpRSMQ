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
use PhpRSMQ\Configs\ConnectionConfigInterface;

/**
 * Connection Interface
 */
interface ConnectionInterface
{
    /**
     * Namespace getter.
     *
     * @return string       Connection namespace.
     */
    public function getNs() :string;

    /**
     * Starts trasaction.
     *
     * @return ConnectionInterface  Self.
     * @throws ConnectionException
     */
    public function multi() :ConnectionInterface;

    /**
     * Executes commands in transaction.
     *
     * @return array|null           Redis result or null if there are not commnads in trasaction.
     * @throws ConnectionException
     */
    public function exec();

    /**
     * Redis time getter.
     *
     * @return array|ConnectionInterface  Redis times hash or self if the function is called in a transaction.
     * @throws ConnectionException
     */
    public function time();

    /**
     * Redis key rename.
     *
     * @param  string                   $oldKey    Old key name.
     * @param  string                   $newKey    New key name.
     * @return bool|ConnectionInterface            Boolean indicates succes of rename or self if the function is called in a transaction.
     * @throws ConnectionException
     */
    public function rename(string $oldKey, string $newKey);

    /**
     * Hash fields getter.
     *
     * @param  string                         $key         Redis hash key.
     * @param  array                          $hashKeys    Keys of hash.
     * @return bool|array|ConnectionInterface              False on failute, redis result hash or self if the function is called in a transaction.
     * @throws ConnectionException
     */
    public function hMGet(string $key, array $hashKeys);

    /**
     * Adds a value to the set.
     *
     * @param  string                       $key    Key of set.
     * @param  string                       $value
     * @return bool|int|ConnectionInterface         False if values exists in the set, number of elements added to set or self if the function is called in a transaction.
     * @throws ConnectionException
     */
    public function sAdd(string $key, string $value);

    /**
     * Removes a value from the set.
     *
     * @param  string                       $key    Key of set.
     * @param  string                       $value
     * @return bool|int|ConnectionInterface         False if values does not exists in the set, number of elements added to set or self if the function is called in a transaction.
     * @throws ConnectionException
     */
    public function sRem(string $key, string $value);

    /**
     * Add value to a sorted set or update its score if it already exists.
     *
     * @param  string                   $key      Sorted set key.
     * @param  float                    $score    Score of value.
     * @param  string                   $value
     * @return int|ConnectionInterface            Number of elements added to set or self if the function is called in a transaction.
     * @throws ConnectionException
     */
    public function zAdd(string $key, float $score, string $value);

    /**
     * Adds a value to the hash stored at key.
     *
     * @param  string                   $key      Key of hash.
     * @param  string                   $hashKey  Key in hash.
     * @param  string                   $value
     * @return int|ConnectionInterface            Number of elements added to set or self if the function is called in a transaction.
     * @throws ConnectionException
     */
    public function hSet(string $key, string $hashKey, string $value);

    /**
     * Adds a value to the hash stored at key (only if this field isn't already in the hash).
     *
     * @param  string                   $key      Key of hash.
     * @param  string                   $hashKey  Key in hash.
     * @param  string                   $value
     * @return bool|ConnectionInterface           True if field was set, false if filed exists or self if the function is called in a transaction.
     * @throws ConnectionException
     */
    public function hSetNx(string $key, string $hashKey, string $value);

    /**
     * Increments the value of a hash key from a hash by a given amount.
     *
     * @param  string                   $key      Key of hash.
     * @param  string                   $value
     * @param  int                      $amount
     * @return int|ConnectionInterface            New value of hash key or self if the function is called in a transaction.
     * @throws ConnectionException
     */
    public function hIncrBy(string $key, string $value, int $amount);
}
