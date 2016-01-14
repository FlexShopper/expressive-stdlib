<?php
namespace StdLib\Fixture\Request;

use StdLib\Request\AgreementFromRequestTrait;

class AgreementFromRequestClass
{
    public $entityManager;

    use AgreementFromRequestTrait;
}