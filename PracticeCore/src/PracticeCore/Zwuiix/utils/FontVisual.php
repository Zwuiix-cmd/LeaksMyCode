<?php

namespace PracticeCore\Zwuiix\utils;

class FontVisual
{
    public static function getE200Symbol(int $line): string
    {
        return match ($line) {
            0 => "", // TODO: SMALL GRAY
            1 => "", // TODO: MEDIUM GRAY
            3 => "", // TODO: BIG GRAY
            4 => "", // TODO: SMALL WHITE
            5 => "", // TODO: BIG WHITE
            6 => "", // TODO: SMALL RED
            7 => "", // TODO: BIG RED
            8 => "", // TODO: SMALL BLUE
            9 => "", // TODO: BIG BLUE
            80 => "", // TODO: STORE LINK
            90 => "", // TODO: DISCORD LINK
        };
    }
}