<?php

namespace PracticeCore\Zwuiix\rank;

class Rank
{
    public function __construct(
        protected string $name,
        protected array $permissions,
        protected string $nameTagFormat,
        protected string $chatFormat,
        protected bool $default = false
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @return string
     */
    public function getNameTagFormat(): string
    {
        return $this->nameTagFormat;
    }

    /**
     * @return string
     */
    public function getChatFormat(): string
    {
        return $this->chatFormat;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }
}