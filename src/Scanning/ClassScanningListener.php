<?php

namespace Lune\Framework\Core\Scanning;

use ReflectionClass;

interface ClassScanningListener
{
    public function beforeScanning();

    public function onScannedClass(ReflectionClass $class);

    public function afterScanning();
}
