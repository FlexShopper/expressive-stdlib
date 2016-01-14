<?php
namespace StdLib\Messaging;

/**
 * Meant to be a light weight DTO
 * to eventually represents an AMPQ message
 *
 * Class Message
 * @package StdLib\Queue
 */
class Message
{
    protected $metadata = [];
    protected $exchange;
    protected $data;

    /**
     * @param string $exchange
     */
    public function __construct($exchange)
    {
        $this->exchange = $exchange;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function toQueue($queue)
    {
        $this->addMetadata('routing_key', $queue);
    }

    public function addMetadata($key, $value)
    {
        $this->metadata[$key] = $value;
    }
}
