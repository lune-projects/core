<?php

namespace Lune\Framework\Core\Registry;

abstract class AbstarctNamedRegistry extends AbstractRegistry
{

    /**
     *
     * @var string
     */
    private $name;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

}
