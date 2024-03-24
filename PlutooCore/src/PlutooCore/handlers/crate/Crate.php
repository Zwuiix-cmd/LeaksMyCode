<?php

namespace PlutooCore\handlers\crate;

class Crate
{
    /**
     * @param string $name
     * @param CrateItem[] $crateItem
     */
    public function __construct(
        protected string $name,
        protected array $crateItem,
    ) {

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CrateItem[]
     */
    public function getLoots(): array
    {
        return $this->crateItem;
    }
}
