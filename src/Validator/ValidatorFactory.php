<?php

namespace StdLib\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use StdLib\Validator\OptionsExtractor;
use Zend\Expressive\Router\RouterInterface;

/**
 * Instantiates the validator
 * Class ValidatorFactory
 * @package SchedulerApi\Validators
 */
class ValidatorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('orm.default');
        return new Validator(
            $container->get(OptionsExtractor::class),
            $container->get(RouterInterface::class),
            $entityManager
        );
    }
}
