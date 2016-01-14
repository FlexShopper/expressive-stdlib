<?php
namespace StdLib\Fixture\Validator;
use StdLib\Validator\ValidationRulesConstructorTrait;
use StdLib\Validator\ValidationRulesInterface;
use Zend\Validator\NotEmpty;

class ValidationRulesFixture implements  ValidationRulesInterface
{

    use ValidationRulesConstructorTrait;

    /**
     * @inheritdoc
     */
    public function getValidationRules()
    {
        return [
            'agreement_id' => [
                NotEmpty::class => [
                    'locale' => 'en'
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMessages()
    {
        return [
            'agreement_id' => [
                NotEmpty::class => 'Please provide an id for the agreement'
            ]
        ];
    }
}