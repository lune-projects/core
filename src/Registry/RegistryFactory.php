<?php

namespace Lune\Framework\Core\Registry;

use ReflectionClass;

final class RegistryFactory
{

    private static $baseRegistryClassName = InMemoryRegistry::class;
    private static $registryDumpLocation = "";

    public static function setBaseRegistryType(string $baseRegistryClassName)
    {
        $reflectionClass = new ReflectionClass($baseRegistryClassName);

        if ($reflectionClass->implementsInterface(Registry::class) || $reflectionClass->isSubclassOf(NamedRegistry::class)) {
            RegistryFactory::$baseRegistryClassName = $baseRegistryClassName;
        } else {
            throw new InvalidRegistryException("A base registry must either implement " . Registry::class . ", or extend " . NamedRegistry::class);
        }
    }

    public static function getBaseRegistry(): Registry
    {
        return new RegistryFactory::$baseRegistryClassName();
    }

    public static function setRegistryDumpLocation(string $location)
    {
        if (file_exists($location) && is_dir($location)) {
            RegistryFactory::$registryDumpLocation = $location;
        }

        throw new InvalidRegistryDumpLocationException("Registry dump location '$location' doesn't exist or is invalid.");
    }

    public static function getRegistryDumpLocation(): string
    {
        return RegistryFactory::$registryDumpLocation;
    }

}
