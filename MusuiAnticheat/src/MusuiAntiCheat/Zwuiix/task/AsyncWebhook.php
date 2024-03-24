<?php

namespace MusuiAntiCheat\Zwuiix\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\thread\NonThreadSafeValue;

class AsyncWebhook extends AsyncTask
{
    public function __construct(
        protected NonThreadSafeValue $url,
        protected NonThreadSafeValue $jsonString,
    ) {}

    public function onRun() : void
    {
        $handle = curl_init($this->url->deserialize());
        curl_setopt($handle, CURLOPT_POSTFIELDS, $this->jsonString->deserialize());
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_exec($handle);
        curl_close($handle);
    }
}