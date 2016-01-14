<?php

namespace StdLib\Fixture\Traits;

use SchedulerApi\Action\PatchTrait;

class PatchTraitClass
{
    use PatchTrait;

    public function __get($name) {
        return $this->$name;
    }
    public function __set($name, $value) {
        $this->$name = $value;
    }
}