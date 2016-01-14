<?php
namespace StdLib\Console;

use Interop\Container\ContainerInterface;

class ApplicationFactory
{
    /**
     * @param ContainerInterface $containe
     * @param bool $loadDev
     * @return Application
     */
    public function __invoke(ContainerInterface $container, $loadDev = null)
    {
        $loadDev = is_null($loadDev) ? defined('IS_DEV') && IS_DEV : $loadDev;
        $config = $container->get('config');
        $app = new Application('Billing Console', $config['app_version']);

        foreach (glob($config['app_root_path'] . '/*modules/*/src/Console/Command/*Command.php') as $file) {
            $devRegex = $loadDev ? '(?:dev-)?' : '';
            if (!preg_match(
                '~'.$devRegex.'modules/([\w\d]+)/src/Console/Command/([\w\d]+Command)\.php~',
                $file,
                $matches
            )
            ) {
                continue;
            }

            $class = $matches[1] . '\\Console\\Command\\' . $matches[2];
            if (class_exists($class)) {
                $app->add($container->get($class));
            }
        }

        return $app;
    }
}
