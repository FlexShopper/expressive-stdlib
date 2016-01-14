<?php
namespace StdLib\Console;

use Symfony\Component\Console\Application as SymfonyConsoleApplication;

class Application extends SymfonyConsoleApplication
{
    /**
     * Constructor.
     *
     * @param string $name The name of the application
     * @param string $version The version of the application
     */
    public function __construct($name = 'Billing Console', $version = null)
    {
        parent::__construct($name, $version);
    }
}
