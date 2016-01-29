<?php

use StdLib\Console\ApplicationFactory;
use StdLib\Console\Application;
use StdLib\Validator\ValidationMiddleware;
use StdLib\Validator\ValidationMiddlewareFactory;
use StdLib\Validator\Validator;
use StdLib\Validator\ValidatorFactory;
use \StdLib\Worker\DirectedManagerAbstractFactory;
use \StdLib\Worker\DirectedWorkerAbstractFactory;

return [
    'dependencies' => [
        'abstract_factories' => [
            DirectedManagerAbstractFactory::class,
            DirectedWorkerAbstractFactory::class,
        ],
        'factories' => [
            Application::class          => ApplicationFactory::class,
            Validator::class            => ValidatorFactory::class,
            ValidationMiddleware::class => ValidationMiddlewareFactory::class,
        ]
    ]
];
