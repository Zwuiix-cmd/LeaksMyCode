<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\PracticeCore;

class KnockbackCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "knockback", "Get the current knockbacks!");
    }

    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.knockback");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $knockbackHandler = KnockbackHandler::getInstance();
        $knockback = $knockbackHandler->getKnockback();
        $heightLimiter = $knockbackHandler->getHeightLimiter();
        $sender->sendMessage(LanguageHandler::getInstance()->translate("server_knockback_1"));
        $sender->sendMessage(LanguageHandler::getInstance()->translate("server_knockback_2", [$knockback->getX()]));
        $sender->sendMessage(LanguageHandler::getInstance()->translate("server_knockback_3", [$knockback->getY()]));
        $sender->sendMessage(LanguageHandler::getInstance()->translate("server_knockback_4"));
        $sender->sendMessage(LanguageHandler::getInstance()->translate("server_knockback_5", [$heightLimiter->getX()]));
        $sender->sendMessage(LanguageHandler::getInstance()->translate("server_knockback_6", [$heightLimiter->getZ()]));
        $sender->sendMessage(LanguageHandler::getInstance()->translate("server_knockback_7", [$heightLimiter->getY()]));
        $sender->sendMessage(LanguageHandler::getInstance()->translate("server_knockback_8", [$knockbackHandler->getAttackCooldown()]));
    }
}