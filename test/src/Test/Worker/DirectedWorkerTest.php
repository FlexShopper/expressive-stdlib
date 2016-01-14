<?php
namespace StdLib\Test\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use StdLib\Worker\DirectedWorker;
use StdLib\Worker\DirectorInterface;

class DirectedWorkerTest extends \PHPUnit_Framework_TestCase
{
    public function testForwardingMessageToDirector()
    {
        $messageMock = $this->getMock(AMQPMessage::class);
        $connectionMock = $this->getMockBuilder(AMQPChannel::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $directorMock = $this->getMock(DirectorInterface::class);
        $directorMock->expects($this->once())
                        ->method('processMessage')
                        ->with($messageMock);

        $directorMock->expects($this->once())
                        ->method('setWorker');

        $directedWorker = new DirectedWorker(
            $connectionMock,
            'test',
            $directorMock
        );


        $directedWorker->processMessage($messageMock);
    }
}