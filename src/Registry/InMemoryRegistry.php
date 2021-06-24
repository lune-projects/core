<?php

namespace Lune\Framework\Core\Registry;

use Closure;

class InMemoryRegistry extends AbstractRegistry
{
    protected $registry;

    public function initialize()
    {
        $this->registry = [];
    }

    public function register(string $key, $value)
    {
        $this->registry[$key] = $value;
    }

    public function contains(string $key): bool
    {
        return array_key_exists($key, $this->registry);
    }

    public function get(string $key)
    {
        return $this->contains($key) ? $this->registry[$key] : null;
    }

    public function isEmpty(): bool
    {
        return count($this->registry) === 0;
    }

    function foreach (Closure $consumer) {
        foreach ($this->registry as $key => $value) {
            if ($consumer($key, $value) === true) {
                break;
            }
        }
    }

    public function persist()
    {
        // Do nothing.
    }

}
