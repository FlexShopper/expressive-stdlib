<?php

use StdLib\Console\ApplicationFactory;
use StdLib\Console\Application;
use \StdLib\Worker\DirectedManagerAbstractFactory;
use \StdLib\Worker\DirectedWorkerAbstractFactory;

return [
    'dependencies' => [
        'abstract_factories' => [
            DirectedManagerAbstractFactory::class,
            DirectedWorkerAbstractFactory::class,
        ],
        'factories' => [
            Application::class => ApplicationFactory::class,
        ]
    ]
];
