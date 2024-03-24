<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\IntegerArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PracticeCore\Zwuiix\PracticeCore;

class SetAttackCooldownCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "setattackcooldown", "Allows you to modify server attackCooldown.");
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.setattackcooldown");
        $this->registerArgument(0, new IntegerArgument("cooldown"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $cooldown = round($args["cooldown"]);
        KnockbackHandler::getInstance()->setAttackCooldown($cooldown);
        $sender->sendMessage(LanguageHandler::getInstance()->translate("set_attack_cooldown", [$cooldown]));
    }
}