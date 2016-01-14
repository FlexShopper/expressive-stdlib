<?php
namespace StdLib\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Generic manager that accepted a DirectorInterface
 * that process a message and returns a new message instance
 * to be forwarded on.
 *
 * Class DirectedManager
 * @package StdLib\Worker
 */
class DirectedManager extends AbstractManager
{
    /**
     * @var DirectorInterface
     */
    private $director;

    /**
     * @param AMQPConnection $conn
     * @param AMQPChannel $generalChannel
     * @param string $generalQueueName
     * @param AMQPChannel $workerQueueChannel
     * @param string $workerExchangeName
     * @param string $workerQueueName
     * @param DirectorInterface $director
     * @param null|string $consumerTag
     */
    public function __construct(
        AMQPConnection $conn,
        AMQPChannel $generalChannel,
        $generalQueueName,
        AMQPChannel $workerQueueChannel,
        $workerExchangeName,
        $workerQueueName,
        DirectorInterface $director,
        $consumerTag = null
    ) {
        parent::__construct(
            $conn,
            $generalChannel,
            $generalQueueName,
            $workerQueueChannel,
            $workerExchangeName,
            $workerQueueName,
            $consumerTag
        );

        $director->setWorker($this);
        $this->director = $director;
    }

    /**
     * Forward the message onto the director and return it's output
     *
     * @param AMQPMessage $message
     * @return AMQPMessage
     */
    public function processMessage(AMQPMessage $message)
    {
        return $this->director->processMessage($message);
    }
}
