<?php

namespace Lune\Framework\Core\Registry;

use Closure;

interface Registry
{
    
    public function initialize();

    public function register(string $key, $value);

    public function contains(string $key): bool;

    public function get(string $key);

    public function isEmpty(): bool;

    public function foreach (Closure $consumer);

    public function persist();

}
