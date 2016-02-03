<?php
namespace StdLib\Test\Console;

use Interop\Container\ContainerInterface;
use StdLib\Console\Application;
use StdLib\Console\ApplicationFactory;
use TestCommand\Console\Command\HelloWorldCommand;

class ApplicationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        require_once TEST_ROOT_PATH .
            '/fixtures/application-factory-test/modules/TestCommand/src/Console/Command/HelloWorldCommand.php';
    }

    public function testLoadCommand()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container->expects($this->any())
                    ->method('get')
                    ->willReturnCallback(function($resource) {
                        switch ($resource) {
                            case 'config':
                                return new \ArrayObject([
                                    'app_console_name' => 'Test',
                                    'app_root_path' => TEST_ROOT_PATH . '/fixtures/application-factory-test',
                                    'app_version'   => '1.0.0-test'
                                ]);
                            default:
                                return new HelloWorldCommand();
                        }
                    });

        $appFactory = new ApplicationFactory();
        /** @var Application $app */
        $app = $appFactory($container);
        $commands = $app->all();
        $this->assertArrayHasKey('test-hello-world', $commands);
    }

    public function testLoadCommandWithBadNamingConvention()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container->expects($this->any())
            ->method('get')
            ->willReturnCallback(function($resource) {
                switch ($resource) {
                    case 'config':
                        return new \ArrayObject([
                            'app_console_name' => 'Test',
                            'app_root_path' => TEST_ROOT_PATH . '/StdLib/fixtures/application-factory-test',
                            'app_version'   => '1.0.0-test'
                        ]);
                    default:
                        return new HelloWorldCommand();
                }
            });

        $appFactory = new ApplicationFactory();
        /** @var Application $app */
        $app = $appFactory($container);
        $commands = $app->all();
        $this->assertArrayNotHasKey('bad-command', $commands);
    }
}