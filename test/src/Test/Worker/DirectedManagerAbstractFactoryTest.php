<?php
namespace StdLib\Test\Worker;

use RuntimeException;
use \stdClass;
use StdLib\Worker\DirectedManagerAbstractFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class DirectedManagerAbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $serviceManager;

    public function setUp()
    {
        $this->serviceManager = $this->getMock(ServiceLocatorInterface::class);

    }

    public function testCanCreateServiceWithNameWrongPrefix()
    {
        $factory = new DirectedManagerAbstractFactory();
        $this->assertFalse(
            $factory->canCreateServiceWithName(
                $this->serviceManager,
                'rawr',
                'rawr'
            )
        );
    }

    public function testCanCreateServiceWithNameNotInConfig()
    {
        $factory = new DirectedManagerAbstractFactory();
        $this->serviceManager->expects($this->once())
                                ->method('get')
                                ->willReturn([
                                    'workers' => []
                                ]);

        $this->assertFalse(
            $factory->canCreateServiceWithName(
                $this->serviceManager,
                DirectedManagerAbstractFactory::MANAGER_PREFIX . 'name',
                DirectedManagerAbstractFactory::MANAGER_PREFIX . 'name'
            )
        );
    }

    public function testCanCreateServiceWithName()
    {
        $factory = new DirectedManagerAbstractFactory();
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->willReturn([
                'workers' => [
                    'name' => []
                ]
            ]);

        $this->assertTrue(
            $factory->canCreateServiceWithName(
                $this->serviceManager,
                DirectedManagerAbstractFactory::MANAGER_PREFIX . 'name',
                DirectedManagerAbstractFactory::MANAGER_PREFIX . 'name'
            )
        );
    }

    public function testCreateServiceWithNameNoDirectorInContainer()
    {
        $this->setExpectedException(RuntimeException::class);

        $factory = new DirectedManagerAbstractFactory();
        $this->serviceManager->expects($this->once())
            ->method('get')
            ->willReturn([
                'workers' => [
                    'name' => [
                        'manager' => [
                            'director' => 'foo'
                        ]
                    ]
                ]
            ]);

        $this->serviceManager->expects($this->once())
                ->method('has')
                ->willReturn(false);

        $this->assertTrue(
            $factory->createServiceWithName(
                $this->serviceManager,
                DirectedManagerAbstractFactory::MANAGER_PREFIX . 'name',
                DirectedManagerAbstractFactory::MANAGER_PREFIX . 'name'
            )
        );

    }


    public function testCreateServiceWithNameDirectorNotImplementingInterface()
    {
        $this->setExpectedException(RuntimeException::class);

        $factory = new DirectedManagerAbstractFactory();
        $this->serviceManager->expects($this->any())
            ->method('get')
            ->willReturnCallback(function ($key) {
                switch ($key) {
                    case 'config':
                        return [
                            'workers' => [
                                'name' => [
                                    'manager' => [
                                        'director' => 'foo'
                                    ]
                                ]
                            ]
                        ];
                    case 'foo':
                        return $this->getMock(stdClass::class);
                }
            });

        $this->serviceManager->expects($this->once())
            ->method('has')
            ->willReturn(true);

        $this->assertTrue(
            $factory->createServiceWithName(
                $this->serviceManager,
                DirectedManagerAbstractFactory::MANAGER_PREFIX . 'name',
                DirectedManagerAbstractFactory::MANAGER_PREFIX . 'name'
            )
        );

    }
}
