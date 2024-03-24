<?php

namespace Zwuiix\Libs\SenseiTarzan\Compement;

use pocketmine\resourcepacks\ZippedResourcePack;

class ZippedResourcePackEncrypted extends ZippedResourcePack
{

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