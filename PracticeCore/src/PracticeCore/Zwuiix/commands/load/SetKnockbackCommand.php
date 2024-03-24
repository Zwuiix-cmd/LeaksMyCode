<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use pocketmine\math\Vector2;
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\FloatArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PracticeCore\Zwuiix\PracticeCore;

class SetKnockbackCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "setknockback", "Allows you to modify server knockbacks.");
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.setknockback");
        $this->registerArgument(0, new FloatArgument("x-z"));
        $this->registerArgument(1, new FloatArgument("y"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $xz = abs($args["x-z"]);
        $y = abs($args["y"]);
        KnockbackHandler::getInstance()->setKnockback(new Vector2($xz, $y));
        $sender->sendMessage(LanguageHandler::getInstance()->translate("set_knockback", [$xz, $y]));
    }
}