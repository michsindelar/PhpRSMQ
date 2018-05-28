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
 * Simple Connection Config
 */
class SimpleConnectionConfig implements ConnectionConfigInterface
{
    /**
     * @var string $ns      Namespace name.
     */
    protected $ns;

    /**
     * @param  string                   $ns  Namespace name. @see SimpleConnectionConfig::setNs()
     * @throws InvalidArgumentException
     */
    public function __construct(string $ns = 'rsmq')
    {
        $this->setNs($ns);
    }

    /**
     * Namespace setter
     *
     * Allowed are alphanumeric characters, hyphens and underscores.
     *
     * @param  string                   $ns  Namespace name.
     * @throws InvalidArgumentException
     */
    protected function setNs(string $ns)
    {
        if(($ns = trim($ns)) != '' && !preg_match('/^([a-zA-Z0-9-_]+)$/', $ns)){
            throw new InvalidArgumentException('Wrong namespace format!');
        }
        $this->ns = $ns;
        return $this;
    }

    /**
     * Namespace getter.
     *
     * @see ConnectionConfigInterface::getNs()
     *
     * @return string     Namespace name.
     */
    public function getNs() :string
    {
        return $this->ns;
    }
}
