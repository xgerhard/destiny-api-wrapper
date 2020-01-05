<?php

namespace Destiny;

class Model
{
    protected $properties = [];
    protected $cached = [];

    public function __construct($aProperties = [])
    {
        $this->properties = $aProperties;
    }

    public function __set($key, $val)
    {
        $this->setProperty($key, $val);
    }

    public function __get($key)
    {
        return $this->getProperty($key);
    }

    protected function setProperty($key, $val)
    {
        $this->properties[$key] = $val;
    }

    protected function getProperty($key)
    {
        if(isset($this->cached[$key]))
            return $this->cached[$key];

        $mutator = 'get'.ucfirst($key);
        $value = $this->properties[$key] ?? null;
        if(is_callable([$this, $mutator]))
            $this->cached[$key] = $value = $this->$mutator($value);

        return $value;
    }
}