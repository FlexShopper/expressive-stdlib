<?php
namespace StdLib\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Base worker class, other classes an extend this to
 * easily build a message processing worker.
 *
 * Class AbstractWorker
 * @package StdLib\Worker
 */
abstract class AbstractWorker implements MessageProcessingWorkerInterface
{
    /**
     * @var bool
     */
    protected $run = false;
    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * Given a channel establish the callback for processing messages
     *
     * @param AMQPChannel $channel
     * @param $queueName
     * @param null $consumerTag
     */
    public function __construct(
        AMQPChannel $channel,
        $queueName,
        $consumerTag = null
    ) {

        $consumerTag = $consumerTag ?: uniqid();
        $channel->basic_consume(
            $queueName,
            $consumerTag,
            false,
            false,
            false,
            false,
            [$this, 'processMessage']
        );

        $this->channel = $channel;
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
        while ($this->run && count($this->channel->callbacks) > 0) {
            $this->channel->wait();
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
        unset($this->channel);
        gc_collect_cycles();
    }
}
