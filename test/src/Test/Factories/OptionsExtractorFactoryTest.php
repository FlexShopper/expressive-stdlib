<?php
namespace StdLib\Test\Factories;

use Billing\Repositories\AgreementRepository;
use Billing\Repositories\PromotionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase;
use SchedulerApi\Action\AgreementAction;
use SchedulerApi\Action\AgreementActionFactory;
use SchedulerApi\Action\PromotionAction;
use SchedulerApi\Action\PromotionActionFactory;
use StdLib\Validator\OptionExtractorFactory;
use StdLib\Validator\OptionsExtractor;
use Zend\Expressive\Router\RouterInterface;

class OptionsExtractorFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testInvokation()
    {
        $container = $this->getMock(ContainerInterface::class);
        $container->expects($this->exactly(2))
            ->method('get')
        ->willReturnCallback(function ($name) {
            if ($name === RouterInterface::class) {
                return $this->getMock(RouterInterface::class);
            } else {
                return [
                    'routes' => []
                ];
            }
        });

        $optionExtractorFactory = new OptionExtractorFactory();
        $optionExtractor = $optionExtractorFactory->__invoke($container);
        $this->assertInstanceOf(OptionsExtractor::class, $optionExtractor);
    }
}