<?php

namespace PlutooCore\command;

use MusuiEssentials\commands\EssentialsCommand;
use MusuiEssentials\MusuiPlayer;
use PlutooCore\handlers\OptionsHandler;
use pocketmine\permission\DefaultPermissions;

class LightningCommand extends EssentialsCommand
{
    protected function prepare(): void
    {
        $this->setOnlyPlayer();
        $this->setPermission(DefaultPermissions::ROOT_USER);
    }

    /**
     * @param MusuiPlayer $player
     * @param array $args
     * @return void
     */
    public function onPlayerRun(MusuiPlayer $player, array $args): void
    {
        $flag = !OptionsHandler::getInstance()->get($player->getName(), "lightning", true);
        OptionsHandler::getInstance()->set($player->getName(), "lightning", $flag);
        $player->sendMessage("§5Vous avez bien changer les lighning vers §9" . ($flag ? "Activé" : "Désactivé") . "§5!");
    }
}