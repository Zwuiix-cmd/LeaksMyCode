<?php

namespace Zwuiix\Interface;

use Closure;
use pocketmine\inventory\Inventory;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use Zwuiix\Libs\muqsit\invmenu\InvMenu;
use Zwuiix\Libs\muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use Zwuiix\Libs\muqsit\invmenu\transaction\InvMenuTransaction;
use Zwuiix\Libs\muqsit\invmenu\transaction\InvMenuTransactionResult;
use Zwuiix\Libs\muqsit\invmenu\type\InvMenuTypeIds;
use Zwuiix\Player\User;
use Zwuiix\Utils\CooldownFormat;

class Stats
{
    use SingletonTrait;

    /**
     * @param User $user
     * @param User $player
     * @return void
     */
    public function send(User $user, User $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setListener(function(InvMenuTransaction $transaction) use($user, $player) : InvMenuTransactionResult{
            $action = $transaction->getAction();
            $item = $action->getInventory()->getItem($action->getSlot());

            if($item->getCustomName() === "§r§9Résultat du dernier combat §7({$player->lastUserFight})") {
                if($user->isConnected()) $this->sendResult($user, $player);
            }

            return $transaction->discard();
        });
        $menu->setInventoryCloseListener(function(User $user, Inventory $inventory) : void {});
        $menu->setName("Informations de §9{$player->getName()}");

        $inv=$menu->getInventory();

        $inv->setItem(0, ItemFactory::getInstance()->get(ItemIds::BOOK)->setCustomName("§r§9Kills: §7{$player->getKills()}"));
        $inv->setItem(1, ItemFactory::getInstance()->get(ItemIds::BOOK)->setCustomName("§r§9Morts: §7{$player->getDeaths()}"));
        $inv->setItem(2, ItemFactory::getInstance()->get(ItemIds::BOOK)->setCustomName("§r§9KillStreak: §7{$player->getKillStreak()}"));
        $inv->setItem(3, ItemFactory::getInstance()->get(ItemIds::BOOK)->setCustomName("§r§9Ratio: §7{$player->getRatio()}"));

        $inv->setItem(5, ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, 22)->setCustomName("§r§9WTAP Percent: §7{$player->getWTAPPercent()}%"));
        $inv->setItem(6, ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD)->setCustomName("§r§9CPS Global: §7{$player->getMoyenneCPS()}cps"));

        $inv->setItem(8, ItemFactory::getInstance()->get(ItemIds::EMERALD)->setCustomName("§r§9Temps de jeu: §7" . TextFormat::clean(CooldownFormat::getInstance()->getFormatBySecond($player->getFirstPlayed() - time()))));

        if(!is_null($player->lastUserFight)) $inv->setItem(26, ItemFactory::getInstance()->get(ItemIds::IRON_INGOT)->setCustomName("§r§9Résultat du dernier combat §7({$player->lastUserFight})"));

        $menu->send($user);
    }

    /**
     * @param User $user
     * @param User $player
     * @return void
     */
    public function sendResult(User $user, User $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setListener(function(InvMenuTransaction $transaction) use($user, $player) : InvMenuTransactionResult{
            $action = $transaction->getAction();
            $item = $action->getInventory()->getItem($action->getSlot());

            if($item->getCustomName() === "§r§9{$player->lastUserFight}") {
                if($user->isConnected()) $this->sendOtherResult($user, $player);
            }

            return $transaction->discard();
        });
        $menu->setName("§9{$player->getName()} §7vs §9{$player->lastUserFight}");

        $inv=$menu->getInventory();
        $inv->setContents($player->lastThisUserFightContent);

        $inv->setItem(51, ItemFactory::getInstance()->get(ItemIds::PAPER)->setCustomName("§r{$player->lastFightMessageDiff}"));
        $inv->setItem(52, ItemFactory::getInstance()->get(ItemIds::PAPER)->setCustomName("§r{$player->lastFightMessageKill}"));
        $inv->setItem(53, ItemFactory::getInstance()->get(ItemIds::PAPER)->setCustomName("§r§9{$player->lastUserFight}"));

        $menu->send($user);
    }

    public function sendOtherResult(User $user, User $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setListener(function(InvMenuTransaction $transaction) use($user, $player) : InvMenuTransactionResult{
            $action = $transaction->getAction();
            $item = $action->getInventory()->getItem($action->getSlot());

            if($item->getCustomName() === "§r§9{$player->getName()}") {
                if($user->isConnected()) $this->sendResult($user, $player);
            }

            return $transaction->discard();
        });
        $menu->setName("§9{$player->getName()} §7vs §9{$player->lastUserFight}");

        $inv=$menu->getInventory();
        $inv->setContents($player->lastUserFightContent);

        $inv->setItem(51, ItemFactory::getInstance()->get(ItemIds::PAPER)->setCustomName("§r{$player->lastFightMessageDiff}"));
        $inv->setItem(52, ItemFactory::getInstance()->get(ItemIds::PAPER)->setCustomName("§r{$player->lastFightMessageKill}"));
        $inv->setItem(53, ItemFactory::getInstance()->get(ItemIds::PAPER)->setCustomName("§r§9{$player->getName()}"));

        $menu->send($user);
    }
}