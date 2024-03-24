<?php

namespace PlutooCore\command;

use MusuiEssentials\libs\CortexPE\Commando\args\RawStringArgument;
use MusuiEssentials\libs\CortexPE\Commando\BaseCommand;
use MusuiEssentials\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PlutooCore\handlers\crate\Crate;
use PlutooCore\handlers\crate\CrateHandler;
use PlutooCore\player\CustomMusuiPlayer;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;

class CrateCommand extends BaseCommand
{
    /**
     * @return void
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
        $this->registerArgument(0, new RawStringArgument("crate", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof CustomMusuiPlayer) return;

        $crate = CrateHandler::getInstance()->getCrateByName($args["crate"]);
        if(!$crate instanceof Crate) {
            $sender->sendMessage( "§cImpossible de trouvé la caisse avec le nom §9{$args["crate"]}§c.");
            return;
        }

        $sender->setCreateCrate(true);
        $sender->setCrate($crate);
        $sender->sendMessage("§aRéaliser un clic droit sur un bloc pour place la caisse §9{$crate->getName()}§a.");
    }
}