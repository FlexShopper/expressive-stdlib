<?php
namespace StdLib\Test\Worker;

use PhpAmqpLib\Channel\AMQPChannel;

class AbstractWorkerTest extends \PHPUnit_Framework_TestCase
{
    protected $channelMock;

    public function setUp()
    {
        $this->channelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public static function setUpBeforeClass()
    {
        if (!class_exists('StdLib\Test\Worker\WorkerFixture')) {
            include __DIR__ . '/WorkerFixture.php';
        }
    }

    public function testStart()
    {
        $directedWorker = new WorkerFixture(
            $this->channelMock,
            'test'
        );

        $directedWorker->start();
        $reflObject = new \ReflectionObject($directedWorker);
        $runProperty = $reflObject->getProperty('run');
        $runProperty->setAccessible(true);
        $this->assertTrue($runProperty->getValue($directedWorker));
    }

    public function testRun()
    {
        $directedWorker = new WorkerFixture(
            $this->channelMock,
            'test'
        );

        $genChannelRefl = new \ReflectionObject($this->channelMock);
        $callbacksProp = $genChannelRefl->getProperty('callbacks');
        $callbacksProp->setAccessible(true);
        $callbacksProp->setValue($this->channelMock, [1, 2]);

        $this->channelMock->expects($this->any())
            ->method('wait')
            ->willReturnCallback(function () use ($callbacksProp) {
                $callbacksProp->setValue($this->channelMock, []);
            });

        $directedWorker->start();
        $reflObject = new \ReflectionObject($directedWorker);
        $runProperty = $reflObject->getProperty('run');
        $runProperty->setAccessible(true);
        $this->assertTrue($runProperty->getValue($directedWorker));
    }

    public function testStop()
    {
        $directedWorker = new WorkerFixture(
            $this->channelMock,
            'test'
        );

        $genChannelRefl = new \ReflectionObject($this->channelMock);
        $callbacksProp = $genChannelRefl->getProperty('callbacks');
        $callbacksProp->setAccessible(true);
        $callbacksProp->setValue($this->channelMock, []);

        $this->channelMock->expects($this->any())
            ->method('wait');

        $directedWorker->start();
        $directedWorker->stop();
        $reflObject = new \ReflectionObject($directedWorker);
        $runProperty = $reflObject->getProperty('run');
        $runProperty->setAccessible(true);
        $this->assertFalse($runProperty->getValue($directedWorker));
    }
}