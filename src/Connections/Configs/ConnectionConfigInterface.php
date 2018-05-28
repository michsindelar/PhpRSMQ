<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ\Connections\Configs;

/**
 * Connection Config Interface
 */
interface ConnectionConfigInterface
{
    /**
     * Namespace getter.
     *
     * Prefix for all keys managed by connection.
     *
     * @return string     Namespace name.
     */
    public function getNs() :string;
}
