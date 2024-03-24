<?php

namespace PracticeCore\Zwuiix\utils;

class DataTranslate
{
    protected Data $data;

    /**
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @return Data
     */
    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * @param string $message
     * @param array $data
     * @return string
     */
    public function translate(string $message, array $data = []): string
    {
        $value = str_replace("{LINE}", "\n", $this->getData()->get($message, $message));
        foreach ($data as $i => $variable) {
            $value = str_replace("{data[{$i}]}", $variable, $value);
        }

        return "{$value}";
    }
}