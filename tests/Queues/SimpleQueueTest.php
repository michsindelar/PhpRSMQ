<?php
namespace PhpRSMQ\Tests\Queues;

use Exception;
use PHPUnit\Framework\TestCase;
use PhpRSMQ\RedisSMQUtils;
use PhpRSMQ\Connections\RedisProxy;
use PhpRSMQ\Messages\TextMessage;
use PhpRSMQ\Queues\SimpleQueue;
use PhpRSMQ\Queues\Configs\SimpleQueueConfig;

class SimpleQueueTest extends TestCase
{
    public function getMockConnection(string ...$args)
    {
        $mockConnection = $this->createMock(RedisProxy::class);
        $objectMethods  = preg_grep('/^[^_]+/', get_class_methods(RedisProxy::class));

        foreach (array_diff($objectMethods, $args) as $method) {
            $mockConnection->expects($this->never())
                           ->method($method);
        }

        return $mockConnection;
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorSyncQueue()
    {
        $mockConnection = $this->getMockConnection('hMGet');

        $mockConnection->expects($this->once())
                       ->method('hMGet')
                       ->willReturn(array(
                           'vt'      => 50,
                           'delay'   => 10,
                           'maxsize' => 1024
                       ));

        new SimpleQueue(new SimpleQueueConfig('test', 50, 10, 1024), $mockConnection);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorCreateQueue()
    {
        $queueName = 'test';
        $vt        = 50;
        $delay     = 10;
        $maxSize   = 1024;
        $time      = array('1519053999', '494416');

        $mockConnection = $this->getMockConnection('hMGet', 'time', 'multi', 'hSetNx', 'sAdd', 'exec');

        $mockConnection->expects($this->once())
                       ->method('hMGet')
                       ->with($queueName . ':Q', array('vt', 'delay', 'maxsize'))
                       ->willReturn(array(
                           'vt'      => false,
                           'delay'   => false,
                           'maxsize' => false
                       ));

        $mockConnection->expects($this->once())
                       ->method('time')
                       ->willReturn($time);

        $mockConnection->expects($this->once())
                       ->method('multi')
                       ->will($this->returnSelf());

        $mockConnection->expects($this->exactly(5))
                       ->method('hSetNx')
                       ->withConsecutive(
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('vt'), $this->equalTo($vt)),
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('delay'), $this->equalTo($delay)),
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('maxsize'), $this->equalTo($maxSize)),
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('created'), $this->equalTo($time[0])),
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('modified'), $this->equalTo($time[0]))
                       )
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('sAdd')
                       ->with($this->equalTo('QUEUES'), $queueName)
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('exec')
                       ->willReturn(array(1 ,1, 1, 1, 1, 1));

        new SimpleQueue(new SimpleQueueConfig($queueName, $vt, $delay, $maxSize), $mockConnection);
    }


    /**
     * @expectedException        PhpRSMQ\Queues\Exceptions\QueueException
     * @expectedExceptionMessage Some problem occurred while trying to create new queue "test"!
     */
    public function testConstructorCreateQueueQueueException()
    {
        $queueName = 'test';
        $vt        = 50;
        $delay     = 10;
        $maxSize   = 1024;
        $time      = array('1519053999', '494416');

        $mockConnection = $this->getMockConnection('hMGet', 'time', 'multi', 'hSetNx', 'sAdd', 'exec');

        $mockConnection->expects($this->once())
                       ->method('hMGet')
                       ->with($queueName . ':Q', array('vt', 'delay', 'maxsize'))
                       ->willReturn(array(
                           'vt'      => false,
                           'delay'   => false,
                           'maxsize' => false
                       ));

        $mockConnection->expects($this->once())
                       ->method('time')
                       ->willReturn($time);

        $mockConnection->expects($this->once())
                       ->method('multi')
                       ->will($this->returnSelf());

        $mockConnection->expects($this->exactly(5))
                       ->method('hSetNx')
                       ->withConsecutive(
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('vt'), $this->equalTo($vt)),
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('delay'), $this->equalTo($delay)),
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('maxsize'), $this->equalTo($maxSize)),
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('created'), $this->equalTo($time[0])),
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('modified'), $this->equalTo($time[0]))
                       )
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('sAdd')
                       ->with($this->equalTo('QUEUES'), $queueName)
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('exec')
                       ->willReturn(array(0 ,1, 0, 1, 1, 1));

        new SimpleQueue(new SimpleQueueConfig($queueName, $vt, $delay, $maxSize), $mockConnection);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorUpdateQueue()
    {
        $queueName  = 'test';
        $oldVt      = 50;
        $newVt      = 60;
        $delay      = 10;
        $oldMaxSize = 1024;
        $newMaxSize = 2048;

        $mockConnection = $this->getMockConnection('hMGet', 'multi', 'hSet', 'exec');

        $mockConnection->expects($this->once())
                       ->method('hMGet')
                       ->with($queueName . ':Q', array('vt', 'delay', 'maxsize'))
                       ->willReturn(array(
                           'vt'      => $oldVt,
                           'delay'   => $delay,
                           'maxsize' => $oldMaxSize
                       ));

        $mockConnection->expects($this->once())
                       ->method('multi')
                       ->will($this->returnSelf());

        $mockConnection->expects($this->exactly(2))
                       ->method('hSet')
                       ->withConsecutive(
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('vt'), $this->equalTo($newVt)),
                           array($this->equalTo($queueName . ':Q'), $this->equalTo('maxsize'), $this->equalTo($newMaxSize))
                       )
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('exec')
                       ->willReturn(array(0, 0));

        new SimpleQueue(new SimpleQueueConfig($queueName, $newVt, $delay, $newMaxSize), $mockConnection);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testValidateMessage()
    {
        $queueName = 'test';
        $vt        = 50;
        $delay     = 10;
        $maxSize   = 1024;

        $mockConnection = $this->getMockConnection('hMGet');

        $mockConnection->expects($this->once())
                       ->method('hMGet')
                       ->willReturn(array(
                           'vt'      => $vt,
                           'delay'   => $delay,
                           'maxsize' => $maxSize
                       ));

        $queue = new SimpleQueue(new SimpleQueueConfig($queueName, $vt, $delay, $maxSize), $mockConnection);
        $queue->validateMessage(new TextMessage('New message!'));
    }

    /**
     * @expectedException        PhpRSMQ\Queues\Exceptions\QueueException
     * @expectedExceptionMessage Message is too long! Max size is 1024 characters!
     */
    public function testValidateMessageQueueException()
    {
        $queueName = 'test';
        $vt        = 50;
        $delay     = 10;
        $maxSize   = 1024;

        $mockConnection = $this->getMockConnection('hMGet');

        $mockConnection->expects($this->once())
                       ->method('hMGet')
                       ->willReturn(array(
                           'vt'      => $vt,
                           'delay'   => $delay,
                           'maxsize' => $maxSize
                       ));

        $queue = new SimpleQueue(new SimpleQueueConfig($queueName, $vt, $delay, $maxSize), $mockConnection);
        $queue->validateMessage(
            new TextMessage(
                'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.' .
                'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.' .
                'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
            )
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSendMessage()
    {
        $queueName = 'test';
        $vt        = 50;
        $delay     = 10;
        $maxSize   = 1024;
        $time      = array('1519053999', '494416');
        $message   = 'New message!';

        // message meta
        $ms = RedisSMQUtils::formatZeroPad($time[1], 6);
        $msgDelay = intval($time[0] . substr($ms, 0, 3)) + $delay * 1000;

        $mockConnection = $this->getMockConnection('hMGet', 'time', 'multi', 'zAdd', 'hSet', 'hIncrBy', 'exec');

        $mockConnection->expects($this->once())
                       ->method('hMGet')
                       ->willReturn(array(
                           'vt'      => $vt,
                           'delay'   => $delay,
                           'maxsize' => $maxSize
                       ));

        $mockConnection->expects($this->once())
                       ->method('time')
                       ->willReturn($time);

        $mockConnection->expects($this->once())
                       ->method('multi')
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('zAdd')
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('hSet')
                       ->with($this->equalTo($queueName . ':Q'), $this->anything(), $this->equalTo($message))
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('hIncrBy')
                       ->with($this->equalTo($queueName . ':Q'), $this->equalTo('totalsent'), $this->equalTo(1))
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('exec')
                       ->willReturn(array(1 ,1, 1));

        $queue = new SimpleQueue(new SimpleQueueConfig($queueName, $vt, $delay, $maxSize), $mockConnection);
        $queue->sendMessage(new TextMessage($message));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetQueueAttributes()
    {
        $oldName   = 'oldName';
        $newName = 'newName';
        $vt        = 50;
        $oldDelay  = 10;
        $newDelay  = 20;
        $maxSize   = 1024;
        $time      = array('1519053999', '494416');
        $message   = 'New message!';

        // message meta

        $mockConnection = $this->getMockConnection('hMGet', 'multi', 'hSet', 'sRem', 'sAdd', 'rename', 'exec');

        $mockConnection->expects($this->once())
                       ->method('hMGet')
                       ->willReturn(array(
                           'vt'      => $vt,
                           'delay'   => $oldDelay,
                           'maxsize' => $maxSize
                       ));

        $mockConnection->expects($this->once())
                       ->method('multi')
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('hSet')
                       ->with($this->equalTo($oldName . ':Q'), $this->equalTo('delay'), $this->equalTo($newDelay))
                       ->will($this->returnSelf());

        $mockConnection->expects($this->exactly(2))
                       ->method('rename')
                       ->withConsecutive(
                          array($this->equalTo($oldName . ':Q'), $this->equalTo($newName . ':Q')),
                          array($this->equalTo($oldName), $this->equalTo($newName))
                       )
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('sRem')
                       ->with($this->equalTo('QUEUES'), $oldName)
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('sAdd')
                       ->with($this->equalTo('QUEUES'), $newName)
                       ->will($this->returnSelf());

        $mockConnection->expects($this->once())
                       ->method('exec')
                       ->willReturn(array(0, '0K', '0K', 1, 1));

        $queue = new SimpleQueue(new SimpleQueueConfig($oldName, $vt, $oldDelay, $maxSize), $mockConnection);
        $queue->setQueueAttributes(new SimpleQueueConfig($newName, $vt, $newDelay, $maxSize));
    }
}
