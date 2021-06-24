<?php

namespace Lune\Framework\Core\Registry;

abstract class AbstractRegistry implements Registry
{
    public function __construct()
    {
        $this->initialize();
    }
}
