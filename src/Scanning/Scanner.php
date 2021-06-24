<?php

namespace Lune\Framework\Core\Scanning;

use Composer\Autoload\ClassLoader;
use Lune\Framework\Core\Scanning\ClassScanningListener;
use Lune\Framework\Core\Util\ClassFileUtils;
use Lune\Framework\Core\Util\StringUtils;
use ReflectionClass;

final class Scanner
{

    /**
     *
     * @var array
     */
    private $prefixes;
    /**
     *
     * @var array<ClassScanningListener>
     */
    private $classScanningListeners = [];
    /**
     *
     * @var ScannedClassNameRegistry
     */
    private $scannedClassNameRegistry;

    public function __construct(ClassLoader $classLoader)
    {
        $this->prefixes = $classLoader->getPrefixesPsr4();
        $this->scannedClassNameRegistry = new ScannedClassNameRegistry();
        $this->scannedClassNameRegistry->initialize();
        $this->instantiateEmbeddedListeners(AspectMapper::class, ComponentDefinitionReader::class, ApiMapper::class);
    }

    public function convertNamespaceToPatterns(string $namespaceName)
    {
        $patterns = [];

        if (array_key_exists($namespaceName, $this->prefixes)) {
            // root namespace
            foreach ($this->prefixes[$namespaceName] as $path) {
                $patterns[] = $path . DIRECTORY_SEPARATOR . "*";
            }
        } else {
            $longestMatchedPrefix = null;
            foreach ($this->prefixes as $prefix => $ignored) {
                if (StringUtils::startsWith($namespaceName, $prefix)) {
                    if ($longestMatchedPrefix === null || strlen($prefix) > strlen($longestMatchedPrefix)) {
                        $longestMatchedPrefix = $prefix;
                    }
                }
            }

            if ($longestMatchedPrefix !== null) {
                $rest = str_replace($longestMatchedPrefix, "", $namespaceName);

                foreach ($this->prefixes[$longestMatchedPrefix] as $path) {
                    $pattern = $path . DIRECTORY_SEPARATOR . str_replace("\\", DIRECTORY_SEPARATOR, $rest) . DIRECTORY_SEPARATOR . "*";
                    $patterns[] = $pattern;
                }
            }
        }

        return $patterns;
    }

    public function scan(array $namespaces)
    {
        if ($this->scannedClassNameRegistry->isEmpty()) {
            foreach ($namespaces as $namespace) {
                $patterns = $this->convertNamespaceToPatterns($namespace);

                foreach ($patterns as $pattern) {
                    foreach (glob($pattern) as $path) {
                        if (is_file($path)) {
                            $scannedClassName = ClassFileUtils::getClassFullNameFromFile($path);
                            $scannedClass = new ReflectionClass($scannedClassName);
                            if ($scannedClass->implementsInterface(ClassScanningListener::class)) {
                                $this->instantiateListener($scannedClassName);
                            } else {
                                $this->scannedClassNameRegistry->register($scannedClassName, $scannedClass);
                            }
                        }
                    }
                }
            }

            $this->emit();

            $this->scannedClassNameRegistry->persist();
        }
    }

    public function emit()
    {
        foreach ($this->classScanningListeners as $listener) {
            $listener->beforeScanning();

            $this->scannedClassNameRegistry->forEach(function ($_, $scannedClass) use ($listener) {
                $listener->onScannedClass($scannedClass);
            });

            $listener->afterScanning();
        }
    }

    private function instantiateEmbeddedListeners(string...$listenerClassNames)
    {
        foreach ($listenerClassNames as $listenerClassName) {
            $this->instantiateListener($listenerClassName);
        }
    }

    private function instantiateListener(string $listenerClassName)
    {
        $listener = new $listenerClassName();
        $this->classScanningListeners[] = $listener;

        if ($listener instanceof ScannedClassNameRegistryAware) {
            $listener->setScannedClassNameRegistry($this->scannedClassNameRegistry);
        }
    }
}
