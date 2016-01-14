<?php
namespace StdLib\Worker;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * A director instructs the manager that holds it
 * how to behave with regard to message processing
 * and general run-time
 *
 * Interface ManagerDirectorInterface
 * @package StdLib\Worker
 */
interface DirectorInterface
{
    /**
     * Instance of the worker that contains the director
     *
     * @param WorkerInterface $worker
     * @return void
     */
    public function setWorker(WorkerInterface $worker);

    /**
     * Message received by worker to be manipulated by director
     *
     * @param AMQPMessage $message
     * @return AMQPMessage
     */
    public function processMessage(AMQPMessage $message);
}
