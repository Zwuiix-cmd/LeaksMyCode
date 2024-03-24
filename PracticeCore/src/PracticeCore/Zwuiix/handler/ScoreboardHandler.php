<?php

namespace PracticeCore\Zwuiix\handler;

use JsonException;
use pocketmine\utils\SingletonTrait;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\utils\Data;
use PracticeCore\Zwuiix\utils\DataTranslate;

class ScoreboardHandler extends DataTranslate
{
    use SingletonTrait;

    /**
     * @throws JsonException
     */
    public function __construct()
    {
        parent::__construct(new Data(PracticeCore::getInstance()->getPlugin()->getDataFolder() . "/scoreboard.ini", Data::INI));
        self::setInstance($this);
    }
}