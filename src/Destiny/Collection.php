<?php

namespace Destiny;

class Collection
{
    public function __get($key)
    {
        $mutator = 'get'. ucfirst($key);
        if(method_exists($this, $mutator))
            return call_user_func([$this, $mutator]);
        else
            return null;
    }
}
