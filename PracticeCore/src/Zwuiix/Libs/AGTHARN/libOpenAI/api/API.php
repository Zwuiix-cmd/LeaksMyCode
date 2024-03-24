<?php

declare(strict_types=1);

namespace Zwuiix\Libs\AGTHARN\libOpenAI\api;

abstract class API
{
    public function __construct(
        public string $apiKey
    ) {
    }
}
