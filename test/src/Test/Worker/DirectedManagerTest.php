<?php
namespace StdLib\Test\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use StdLib\Worker\DirectedManager;
use StdLib\Worker\DirectorInterface;

class DirectedManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testForwardingMessageToDirector()
    {
        $messageMock = $this->getMock(AMQPMessage::class);

        $connectionMock = $this->getMockBuilder(AMQPConnection::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $generalChannelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $workerChannelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $directorMock = $this->getMock(DirectorInterface::class);
        $directorMock->expects($this->once())
            ->method('processMessage')
            ->with($messageMock);

        $directorMock->expects($this->once())
            ->method('setWorker');

        $directedWorker = new DirectedManager(
            $connectionMock,
            $generalChannelMock,
            'test',
            $workerChannelMock,
            'test-exchange',
            'worker',
            $directorMock
        );


        $directedWorker->processMessage($messageMock);
    }
}
