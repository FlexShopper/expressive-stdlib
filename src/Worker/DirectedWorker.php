<?php
namespace StdLib\Worker;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Generic worker that takes a DirectorInterface
 * which processes the message.
 *
 * Class DirectedWorker
 * @package StdLib\Worker
 */
class DirectedWorker extends AbstractWorker
{
    /**
     * @var DirectorInterface
     */
    private $director;

    /**
     * @inheritdoc
     *
     * Also take a director and use that to manipulate the messages
     *
     * @param AMQPChannel $channel
     * @param string $queueName
     * @param DirectorInterface $director
     */
    public function __construct(
        AMQPChannel $channel,
        $queueName,
        DirectorInterface $director
    ) {
        parent::__construct($channel, $queueName);

        $director->setWorker($this);

        $this->director = $director;
    }

    /**
     * @inheritdoc
     */
    public function processMessage(AMQPMessage $message)
    {
        return $this->director->processMessage($message);
    }
}
