<?php
namespace StdLib\Test\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AbstractManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $generalChannelMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $workerChannelMock;

    public static function setUpBeforeClass()
    {
        if (!class_exists('StdLib\Test\Worker\ManagerFixture')) {
            include __DIR__ . '/ManagerFixture.php';
        }
    }

    public function setUp()
    {
        $this->connectionMock = $this->getMockBuilder(AMQPConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->generalChannelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->workerChannelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testBasicConsumeIsExecuted()
    {
        $this->generalChannelMock->expects($this->once())
                    ->method('basic_consume');

        $directedWorker = new ManagerFixture(
            $this->connectionMock,
            $this->generalChannelMock,
            'test',
            $this->workerChannelMock,
            'test-exchange',
            'worker'
        );
    }

    public function testForwardToWorker()
    {
        $this->workerChannelMock->expects($this->once())
                            ->method('basic_publish');

        $directedWorker = new ManagerFixture(
            $this->connectionMock,
            $this->generalChannelMock,
            'test',
            $this->workerChannelMock,
            'test-exchange',
            'worker'
        );

        $directedWorker->forwardToWorker($this->getMock(AMQPMessage::class));
    }

    public function testStart()
    {
        $directedWorker = new ManagerFixture(
            $this->connectionMock,
            $this->generalChannelMock,
            'test',
            $this->workerChannelMock,
            'test-exchange',
            'worker'
        );

        $directedWorker->start();
        $reflObject = new \ReflectionObject($directedWorker);
        $runProperty = $reflObject->getProperty('run');
        $runProperty->setAccessible(true);
        $this->assertTrue($runProperty->getValue($directedWorker));
    }

    public function testRun()
    {
        $directedWorker = new ManagerFixture(
            $this->connectionMock,
            $this->generalChannelMock,
            'test',
            $this->workerChannelMock,
            'test-exchange',
            'worker'
        );

        $genChannelRefl = new \ReflectionObject($this->generalChannelMock);
        $callbacksProp = $genChannelRefl->getProperty('callbacks');
        $callbacksProp->setAccessible(true);
        $callbacksProp->setValue($this->generalChannelMock, [1, 2]);

        $this->generalChannelMock->expects($this->any())
                                    ->method('wait')
                                    ->willReturnCallback(function () use ($callbacksProp) {
                                        $callbacksProp->setValue($this->generalChannelMock, []);
                                    });

        $directedWorker->start();
        $reflObject = new \ReflectionObject($directedWorker);
        $runProperty = $reflObject->getProperty('run');
        $runProperty->setAccessible(true);
        $this->assertTrue($runProperty->getValue($directedWorker));
    }

    public function testStop()
    {
        $directedWorker = new ManagerFixture(
            $this->connectionMock,
            $this->generalChannelMock,
            'test',
            $this->workerChannelMock,
            'test-exchange',
            'worker'
        );

        $genChannelRefl = new \ReflectionObject($this->generalChannelMock);
        $callbacksProp = $genChannelRefl->getProperty('callbacks');
        $callbacksProp->setAccessible(true);
        $callbacksProp->setValue($this->generalChannelMock, []);

        $this->generalChannelMock->expects($this->any())
            ->method('wait');

        $directedWorker->start();
        $directedWorker->stop();
        $reflObject = new \ReflectionObject($directedWorker);
        $runProperty = $reflObject->getProperty('run');
        $runProperty->setAccessible(true);
        $this->assertFalse($runProperty->getValue($directedWorker));
    }

    public function testShutdown()
    {
        $directedWorker = new ManagerFixture(
            $this->connectionMock,
            $this->generalChannelMock,
            'test',
            $this->workerChannelMock,
            'test-exchange',
            'worker'
        );

        $this->connectionMock->expects($this->any())
            ->method('__destruct');

        $this->generalChannelMock->expects($this->any())
            ->method('__destruct');

        $this->workerChannelMock->expects($this->any())
            ->method('__destruct');

        $directedWorker->shutdown();
    }
}
