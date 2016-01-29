<?php

namespace StdLib\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use StdLib\Doctrine\EmptyEntityManager;
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
        if ($container->has('orm.default')) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $container->get('orm.default');
        } else {
            $entityManager = new EmptyEntityManager();
        }

        return new Validator(
            $container->get(OptionsExtractor::class),
            $container->get(RouterInterface::class),
            $entityManager
        );
    }
}
