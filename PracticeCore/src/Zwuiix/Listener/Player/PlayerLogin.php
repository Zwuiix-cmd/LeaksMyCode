<?php

namespace Zwuiix\Listener\Player;

use Exception;
use JsonException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use Zwuiix\Handler\AntiCheatHandler;
use Zwuiix\Handler\Rank\RankHandler;
use Zwuiix\handler\Sanction;
use Zwuiix\Main;
use Zwuiix\Player\User;
use Zwuiix\Utils\Skin;
use Zwuiix\Utils\TransferUtils;

class PlayerLogin implements Listener
{

    public Main $plugin;
    public function __construct(Main $main){
        $this->plugin=$main;
    }

    /**
     * @param PlayerLoginEvent $event
     * @return void
     * @throws JsonException
     * @throws Exception
     */
    public function onLogin(PlayerLoginEvent $event): void
    {
        $player=$event->getPlayer();
        $info = $player->getNetworkSession()->getPlayerInfo();
        if(!$player instanceof User)return;

        if (Skin::getInstance()->checkSkin($player)) $player->setSkin(Skin::getInstance()->randomSkin());
        AntiCheatHandler::getInstance()->initializePlayer($player, true);
    }
}