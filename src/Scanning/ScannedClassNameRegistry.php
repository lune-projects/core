<?php

namespace Lune\Framework\Core\Scanning;

use Closure;
use Lune\Framework\Core\Registry\Registry;
use Lune\Framework\Core\Registry\RegistryFactory;

class ScannedClassNameRegistry implements Registry
{

    /**
     *
     * @var Registry
     */
    private $registry;

    public function __construct()
    {
        $this->registry = RegistryFactory::getBaseRegistry();
    }

    public function initialize()
    {
        $this->registry->initialize();
    }

    public function register(string $key, $value)
    {
        $this->registry->register($key, $value);
    }

    public function contains(string $key): bool
    {
        return $this->registry->contains($key);
    }

    public function get(string $key)
    {
        return $this->registry->get($key);
    }

    public function isEmpty(): bool
    {
        return $this->registry->isEmpty();
    }

    function foreach (Closure $consumer) {
        $this->registry->forEach($consumer);
    }

    public function persist()
    {
        $this->registry->persist();
    }

}
