<?php

namespace PracticeCore\Zwuiix\session;

use pocketmine\lang\Translatable;
use pocketmine\player\chat\ChatFormatter as ChatFormatterPM;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use PracticeCore\Zwuiix\handler\RankHandler;

class ChatFormatter implements ChatFormatterPM
{
    public function __construct(
        protected Info $info
    ) {}

    public function format(string $username, string $message): Translatable|string
    {
        $isDisguise = $this->info->getSession()->isDisguise();
        [
            $name,
            $rank
        ] = [
            ($isDisguise ? $this->info->getSession()->getDisguiseName() : $this->info->getSession()->getName()),
            ($isDisguise ? RankHandler::getInstance()->getDefaultRank() : $this->info->getRank())
        ];

        return str_replace(["{RANK}", "{NAME}", "{MESSAGE}"], [$rank->getName(), $name, Server::getInstance()->isOp($username) ? $message : TextFormat::clean($message)], $rank->getChatFormat());
    }
}