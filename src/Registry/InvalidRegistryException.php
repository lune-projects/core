<?php

namespace Lune\Framework\Core\Registry;

use RuntimeException;

class InvalidRegistryException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
