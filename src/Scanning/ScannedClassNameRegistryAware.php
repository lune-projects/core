<?php

namespace Lune\Framework\Core\Scanning;

interface ScannedClassNameRegistryAware
{
    public function setScannedClassNameRegistry(ScannedClassNameRegistry $registry);
}
