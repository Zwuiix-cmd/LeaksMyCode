<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Server;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\TargetPlayerArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\TextArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;

class TellCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "tell", "Send a private message to someone!", ["w", "msg"]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.tell");
        $this->registerArgument(0, new TargetPlayerArgument(false, "name"));
        $this->registerArgument(1, new TextArgument("text"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("you_are_not_player"));
            return;
        }

        $session = Server::getInstance()->getPlayerByPrefix($args["name"]);
        if(!$session instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("player_not_online"));
            return;
        }

        if($sender->getUniqueId() === $session->getUniqueId()) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("tell_yourself"));
            return;
        }

        if(!$session->getInfo()->hasPrivateMessages()) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("tell_not_authorize"));
            return;
        }

        $text = $args["text"];
        $sender->sendMessage(LanguageHandler::getInstance()->translate("tell_sender", [$session->getName(), $text]));
        $session->sendMessage(LanguageHandler::getInstance()->translate("tell_receive", [$sender->getName(), $text]));
        $sender->setReplySession($session);
        $session->setReplySession($sender);

        if($session->getInfo()->hasPrivateMessagesSounds()) {
            $pos = $session->getPosition();
            $session->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("note.flute", $pos->x, $pos->y, $pos->z, 1, 1));
        }
    }
}