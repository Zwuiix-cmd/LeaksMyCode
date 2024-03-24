<?php

namespace Zwuiix\Listener\Player;

use JsonException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChangeSkinEvent;
use Zwuiix\Player\User;
use Zwuiix\Utils\Skin;

class PlayerChangeSkin implements Listener
{
    private ?Skin $defaultSkin = null;

    /**
     * @priority HIGH
     * @throws JsonException
     */
    public function onSkinChange(PlayerChangeSkinEvent $event): void
    {
        $player = $event->getPlayer();
        if(!$player instanceof User)return;
        $new=$event->getNewSkin();
        if (Skin::getInstance()->checkSkin($player,$new->getSkinData())) {
            $event->setNewSkin(Skin::getInstance()->randomSkin());
            $player->setSkin(Skin::getInstance()->randomSkin());
        }
        $event->cancel();
    }
}