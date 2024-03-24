<?php

namespace PracticeCore\Zwuiix\server;

class ServerQueryResponse
{
    /**
     * @param string[] $response
     */
    public function __construct(
        protected array $response,
    ) {}

    /**
     * @return string[]
     */
    public function getData(): array
    {
        return $this->response;
    }
}