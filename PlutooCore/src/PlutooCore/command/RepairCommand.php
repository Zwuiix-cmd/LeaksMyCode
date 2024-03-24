<?php

namespace PlutooCore\command;

use JsonException;
use MusuiEssentials\commands\EssentialsCommand;
use MusuiEssentials\managers\CooldownManager;
use MusuiEssentials\managers\PermissionManager;
use MusuiEssentials\MusuiPlayer;
use MusuiEssentials\utils\Cooldown;
use MusuiEssentials\utils\DateFormatter;
use PlutooCore\command\sub\RepairAllCommand;
use pocketmine\item\Durable;

class RepairCommand extends EssentialsCommand
{
    /**
     * @return void
     * @throws JsonException
     */
    protected function prepare(): void
    {
        $this->setOnlyPlayer();
        $this->setPermission(PermissionManager::create("repair.use")->getName());
        $this->registerSubCommand(new RepairAllCommand(\MusuiEssentials::getInstance(), "all"));
    }

    /**
     * @param MusuiPlayer $player
     * @param array $args
     * @return void
     */
    public function onPlayerRun(MusuiPlayer $player, array $args): void
    {
        $cooldownTime = 30;
        $cooldown = CooldownManager::getInstance()->get($player, $this->getName());
        if($cooldown instanceof Cooldown && $cooldown->isInCooldown()) {
            $player->sendMessage(Cooldown::inCooldownResponse($cooldown));
            return;
        }

        $item = $player->getInventory()->getItemInHand();
        if($item->isNull()) {
            $player->sendMessage("§cItem invalide!");
            return;
        }

        if(!$item instanceof Durable) {
            $player->sendMessage("§cVous ne pouvez pas réparé cet item.");
            return;
        }

        $item->setDamage(0);
        $item->getNamedTag()->setInt("durability", $item->getMaxDurability());
        $player->getInventory()->setItemInHand($item);
        $player->sendMessage("§5Vous avez bien réparé votre item!");
        CooldownManager::getInstance()->setCooldown($player, $this->getName(), DateFormatter::toInt($cooldownTime));
    }
}