<?php
namespace StdLib\Test\Worker;

use PhpAmqpLib\Message\AMQPMessage;
use StdLib\Worker\AbstractWorker;

class WorkerFixture extends AbstractWorker
{
    /**
     * @param AMQPMessage $message
     * @return AMQPMessage
     */
    public function processMessage(AMQPMessage $message)
    {
        return new AMQPMessage($message->body);
    }

}