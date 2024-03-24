<?php

namespace PlutooCore\command;

use MusuiEssentials\libs\CortexPE\Commando\args\RawStringArgument;
use MusuiEssentials\libs\CortexPE\Commando\BaseCommand;
use MusuiEssentials\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PlutooCore\handlers\crate\Crate;
use PlutooCore\handlers\crate\CrateHandler;
use PlutooCore\player\CustomMusuiPlayer;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\Server;

class KeyCommand extends BaseCommand
{
    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
        $this->registerArgument(0, new RawStringArgument("crate", false));
        $this->registerArgument(1, new RawStringArgument("player", false));
        $this->registerArgument(2, new RawStringArgument("count", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof CustomMusuiPlayer) return;

        $crate = CrateHandler::getInstance()->getCrateByName($args["crate"]);
        if(!$crate instanceof Crate) {
            $sender->sendMessage( "§cImpossible de trouvé la caisse avec le nom §9{$args["crate"]}§c.");
            return;
        }

        $target = Server::getInstance()->getPlayerExact($args["player"]);
        if(!$target instanceof CustomMusuiPlayer) {
            $sender->sendMessage("§cImpossible de trouver le joueur.");
            return;
        }

        $count = $args["count"] ?? 1;
        $item = clone VanillaBlocks::TRIPWIRE_HOOK()->asItem();
        $item->getNamedTag()->setString("key", $crate->getName());
        $item->setCustomName("§r§9Clé {$crate->getName()}");
        $item->setCount($count);
        ($inv = $target->getInventory())->canAddItem($item) ? $inv->addItem($item) : $target->getWorld()->dropItem($target->getPosition(), $item);

    }
}