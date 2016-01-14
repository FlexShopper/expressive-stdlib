<?php
namespace StdLib\Worker;

use PhpAmqpLib\Connection\AMQPConnection;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DirectedWorkerAbstractFactory implements AbstractFactoryInterface
{
    const WORKER_PREFIX = 'task.worker.';

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
        if (substr($name, 0, strlen(self::WORKER_PREFIX)) != self::WORKER_PREFIX) {
            return false;
        }

        $config = $serviceLocator->get('config')['workers'];
        $key = substr($requestedName, strlen(self::WORKER_PREFIX));
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
        $name = substr($requestedName, strlen(self::WORKER_PREFIX));
        $config = $serviceLocator->get('config');
        $workerConfig = $config['workers'][$name];

        if (!$serviceLocator->has($workerConfig['worker']['director'])) {
            throw new \RuntimeException("Could not load {$workerConfig['worker']['director']}");
        }

        $director = $serviceLocator->get($workerConfig['worker']['director']);
        if (!$director instanceof DirectorInterface) {
            throw new \RuntimeException("Could not load {$workerConfig['worker']['director']}");
        }

        $rabbitMqSettings = isset($workerConfig['rabbitmq_settings']) ?
            $workerConfig['rabbitmq_settings'] :  $config['rabbitmq_settings'];

        $conn = new AMQPConnection(
            $rabbitMqSettings['host'],
            $rabbitMqSettings['port'],
            $rabbitMqSettings['username'],
            $rabbitMqSettings['password']
        );

        // Bind to the one specific for the workers
        $exchange = $workerConfig['worker']['exchange']['name'];
        $exchangeType = $workerConfig['worker']['exchange']['type'];
        $processorQueueName = $workerConfig['worker']['queue']['name'];
        $routingKey = isset($workerConfig['worker']['queue']['routing_key']) ?
                            $workerConfig['worker']['queue']['routing_key'] : null;
        $workerChannel = $conn->channel();
        $workerChannel->queue_declare($processorQueueName, false, true, false, false);
        $workerChannel->exchange_declare($exchange, $exchangeType, false, false, false);
        $workerChannel->queue_bind($processorQueueName, $exchange, $routingKey);


        return new DirectedWorker(
            $workerChannel,
            $processorQueueName,
            $director
        );
    }
}
