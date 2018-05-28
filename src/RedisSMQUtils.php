<?php
/**
 * PHP Redis Simple Message Queue
 *
 * @author    Michal Sindelar <mich.sindelar@gmail.com>
 * @link      https://github.com/michsindelar/PhpRSMQ
 * @license   MIT
 */
namespace PhpRSMQ;

use InvalidArgumentException;
use PhpRSMQ\Connections\ConnectionsInterface;

/**
 * Redis SMQ Utilities
 */
class RedisSMQUtils
{
    /**
     * @param    int                     $len  This is the lenght of id.
     * @return   string                        Id in string format.
     * @throws InvalidArgumentException        If parameters are nagative or not integers.
     */
    public static function makeId(int $len) :string
    {
        if($len < 0){
            throw new InvalidArgumentException('Lenght must be a non-negative number!');
        }

        $text     = '';
        $possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for($i=0; $i < $len; $i++){
            $text .= $possible{(int) floor(rand(0, strlen($possible) - 1))};
        }
        return $text;
    }

    /**
     * Pad a non-negative integer presented by param $num with leading zeros
     *
     * @param  int  $num
     * @param  int                      $count  Final count of digits.
     * @return int                              Number with zero padding.
     * @throws InvalidArgumentException         If parameters are nagative or not integers.
     */
    public static function formatZeroPad(int $num, int $count) :string
    {
        if($num < 0 || $count < 0){
            throw new InvalidArgumentException('Arguments must be non-negative numbers!');
        }
        if(strlen((string) $num) >= $count){
            return $num;
        }
        return substr(pow(10, $count) + $num, 1);
    }
}
