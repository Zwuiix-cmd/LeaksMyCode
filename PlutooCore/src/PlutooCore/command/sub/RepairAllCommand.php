<?php

namespace PlutooCore\command\sub;

use JsonException;
use MusuiEssentials\commands\EssentialsSubCommand;
use MusuiEssentials\managers\CooldownManager;
use MusuiEssentials\managers\PermissionManager;
use MusuiEssentials\MusuiPlayer;
use MusuiEssentials\utils\Cooldown;
use MusuiEssentials\utils\DateFormatter;
use pocketmine\item\Durable;

class RepairAllCommand extends EssentialsSubCommand
{

    /**
     * @return void
     * @throws JsonException
     */
    protected function prepare(): void
    {
        $this->setOnlyPlayer();
        $this->setPermission(PermissionManager::create("repair.all")->getName());
    }

    /**
     * @param MusuiPlayer $player
     * @param array $args
     * @return void
     */
    public function onPlayerRun(MusuiPlayer $player, array $args): void
    {
        $cooldownTime = 600;
        $cooldown = CooldownManager::getInstance()->get($player, $this->getName());
        if($cooldown instanceof Cooldown && $cooldown->isInCooldown()) {
            $player->sendMessage(Cooldown::inCooldownResponse($cooldown));
            return;
        }

        $repaired = 0;
        foreach ($player->getArmorInventory()->getContents() as $key => $item) {
            if($item->isNull()) continue;
            if(!$item instanceof Durable) continue;

            $item->setDamage(0);
            $item->getNamedTag()->setInt("durability", $item->getMaxDurability());
            $player->getArmorInventory()->setItem($key, $item);
            $repaired++;
        }
        foreach ($player->getInventory()->getContents() as $key => $item) {
            if($item->isNull()) continue;
            if(!$item instanceof Durable) continue;

            $item->setDamage(0);
            $item->getNamedTag()->setInt("durability", $item->getMaxDurability());
            $player->getInventory()->setItem($key, $item);
            $repaired++;
        }

        $player->sendMessage("§5Vous avez bien réparés §9{$repaired} items§5!");
        CooldownManager::getInstance()->setCooldown($player, $this->getName(), DateFormatter::toInt($cooldownTime));
    }
}