<?php
namespace StdLib\Worker;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * Interface for defining a worker which processes messages
 *
 * Interface MessageProcessingWorkerInterface
 * @package StdLib\Worker
 */
interface MessageProcessingWorkerInterface extends WorkerInterface
{
    /**
     * Intake a message and process it
     *
     * @param AMQPMessage $message
     * @return AMQPMessage
     */
    public function processMessage(AMQPMessage $message);
}
