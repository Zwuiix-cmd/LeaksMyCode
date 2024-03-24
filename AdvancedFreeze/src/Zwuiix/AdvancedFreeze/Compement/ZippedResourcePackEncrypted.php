<?php

namespace Zwuiix\AdvancedFreeze\Compement;

use pocketmine\resourcepacks\ZippedResourcePack;

class ZippedResourcePackEncrypted extends ZippedResourcePack
{

    /**
     * @var string
     */
    private string $encryptKey = "";


    /**
     * @param string $encryptKey
     */
    public function setEncryptKey(string $encryptKey): void
    {
        $this->encryptKey = $encryptKey;
    }

    /**
     * @return string
     */
    public function getEncryptKey(): string
    {
        return $this->encryptKey;
    }
}