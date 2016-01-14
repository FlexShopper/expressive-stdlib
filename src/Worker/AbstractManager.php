<?php
namespace StdLib\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

abstract class AbstractManager implements MessageProcessingWorkerInterface
{
    /**
     * @var bool
     */
    protected $run = false;
    /**
     * @var AMQPChannel
     */
    private $generalChannel;
    /**
     * @var AMQPChannel
     */
    private $workerQueueChannel;
    /**
     * @var AMQPConnection
     */
    private $conn;
    /**
     * @var string
     */
    private $workerExchangeName;

    /**
     * @param AMQPConnection $conn
     * @param AMQPChannel $generalChannel
     * @param string $generalQueueName
     * @param AMQPChannel $workerQueueChannel
     * @param string $workerExchangeName
     * @param string $workerQueueName
     * @param null|string $consumerTag
     */
    public function __construct(
        AMQPConnection $conn,
        AMQPChannel $generalChannel,
        $generalQueueName,
        AMQPChannel $workerQueueChannel,
        $workerExchangeName,
        $workerQueueName,
        $consumerTag = null
    ) {
        $consumerTag = $consumerTag ?: uniqid();
        $generalChannel->basic_consume(
            $generalQueueName,
            $consumerTag,
            false,
            false,
            false,
            false,
            [$this, 'forwardToWorker']
        );

        $this->generalChannel      = $generalChannel;
        $this->workerQueueChannel  = $workerQueueChannel;
        $this->conn                = $conn;
        $this->workerExchangeName  = $workerExchangeName;
    }

    /**
     * Send the message to our worker queue.
     *
     * @param AMQPMessage $message
     */
    public function forwardToWorker(AMQPMessage $message)
    {
        $this->workerQueueChannel->basic_publish(
            $this->processMessage($message),
            $this->workerExchangeName
        );
    }

    /**
     * Start the worker process
     *
     * @return void
     */
    public function start()
    {
        $this->run = true;
        $this->run();
        $this->shutdown();
    }

    /**
     * Worker process running
     *
     * @return void
     */
    public function run()
    {
        while ($this->run && count($this->generalChannel->callbacks) > 0) {
            $this->generalChannel->wait();
        }
    }

    /**
     * Stop worker
     *
     * @return void
     */
    public function stop()
    {
        $this->run = false;
    }

    /**
     * Handle shutdown procedure
     *
     * @return mixed
     */
    public function shutdown()
    {
        // ear mark variables to be removed (and thus call __destruct) on gc collect
        unset($this->generalChannel);
        unset($this->workerQueueChannel);
        unset($this->conn);

        // now force the gc process so that the destructor is called
        gc_collect_cycles();
    }
}
