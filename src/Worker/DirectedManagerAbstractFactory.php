<?php
namespace StdLib\Worker;

use PhpAmqpLib\Connection\AMQPConnection;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DirectedManagerAbstractFactory implements AbstractFactoryInterface
{
    const MANAGER_PREFIX = 'task.manager.';

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (substr($name, 0, strlen(self::MANAGER_PREFIX)) != self::MANAGER_PREFIX) {
            return false;
        }

        $config = $serviceLocator->get('config')['workers'];
        $key = substr($requestedName, strlen(self::MANAGER_PREFIX));
        if (!isset($config[$key])) {
            return false;
        }

        return true;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $name = substr($requestedName, strlen(self::MANAGER_PREFIX));
        $config = $serviceLocator->get('config');
        $workerConfig = $config['workers'][$name];

        if (!$serviceLocator->has($workerConfig['manager']['director'])) {
            throw new \RuntimeException("Could not load {$workerConfig['manager']['director']}");
        }

        $director = $serviceLocator->get($workerConfig['manager']['director']);
        if (!$director instanceof DirectorInterface) {
            throw new \RuntimeException("Could not load {$workerConfig['manager']['director']}");
        }

        $rabbitMqSettings = isset($workerConfig['rabbitmq_settings']) ?
                                $workerConfig['rabbitmq_settings'] :  $config['rabbitmq_settings'];

        $conn = new AMQPConnection(
            $rabbitMqSettings['host'],
            $rabbitMqSettings['port'],
            $rabbitMqSettings['username'],
            $rabbitMqSettings['password']
        );

        // Bind to the generic exchange
        $eventChannel = $conn->channel();

        $exchange = $workerConfig['manager']['general']['exchange']['name'];
        $exchangeType = $workerConfig['manager']['general']['exchange']['type'];

        $eventQueueName = $workerConfig['manager']['general']['queue']['name'];
        $routingKey = isset($workerConfig['manager']['general']['queue']['routing_key']) ?
                        $workerConfig['manager']['general']['queue']['routing_key'] : null;

        $eventChannel->queue_declare($eventQueueName, false, true, false, false);
        $eventChannel->exchange_declare($exchange, $exchangeType, false, false, true);
        $eventChannel->queue_bind($eventQueueName, $exchange, $routingKey);

        // Bind to the one specific for the workers
        $processorQueueName = $workerConfig['manager']['worker']['queue']['name'];
        $workerExchange = $workerConfig['manager']['worker']['exchange']['name'];
        $workerExchangeType = $workerConfig['manager']['worker']['exchange']['type'];
        $workerChannel = $conn->channel();
        $workerChannel->exchange_declare($workerExchange, $workerExchangeType, false, false, false);
        $workerChannel->queue_declare($processorQueueName, false, true, false, false);
        $workerChannel->queue_bind($processorQueueName, $workerExchange);

        return new DirectedManager(
            $conn,
            $eventChannel,
            $eventQueueName,
            $workerChannel,
            $workerExchange,
            $processorQueueName,
            $director
        );
    }
}
