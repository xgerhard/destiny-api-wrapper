<?php

namespace Destiny\Definitions;

class Definition
{
    public function getOptions()
    {
        $reflector = new \ReflectionClass(get_class($this));
        return $reflector->getConstants();
    }
}