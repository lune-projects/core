<?php

namespace Lune\Framework\Core\Registry;

use Closure;

class FilesystemRegistry extends AbstarctNamedRegistry
{
    protected $registry;

    public function initialize()
    {
        $registryDumpLocation = $this->getDumpFileLocation();

        if (file_exists($registryDumpLocation)) {
            $this->registry = unserialize(file_get_contents($registryDumpLocation)) ?? [];
        }

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

    public function foreach (Closure $consumer) {
        foreach ($this->registry as $key => $value) {
            if ($consumer($key, $value) === true) {
                break;
            }
        }
    }

    public function persist()
    {
        $fp = fopen($this->getDumpFileLocation(), "w");
        if ($fp) {
            fwrite($fp, serialize($this->registry));
            fclose($fp);
        } else {
            echo "Failed to write mapping info to file. Please make sure Apache has the write permision.";
            exit();
        }
    }

    private function getDumpFileLocation(): string
    {
        return RegistryFactory::getRegistryDumpLocation() . $this->getName();
    }

}
