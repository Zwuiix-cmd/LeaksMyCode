<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\TextArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\session\Session;

class ReplyCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "reply", "Send a private message to someone!", ["r"]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.reply");
        $this->registerArgument(0, new TextArgument("text"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Session) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("you_are_not_player"));
            return;
        }

        if(!$sender->hasReplySession()) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("not_have_reply_session"));
            return;
        }

        $session = $sender->getReplySession();
        if(!$session->isConnected()) {
            $sender->sendMessage(LanguageHandler::getInstance()->translate("player_not_online"));
            return;
        }

        $text = $args["text"];
        $sender->sendMessage(LanguageHandler::getInstance()->translate("tell_sender", [$session->getName(), $text]));
        $session->sendMessage(LanguageHandler::getInstance()->translate("tell_receive", [$sender->getName(), $text]));

        if($session->getInfo()->hasPrivateMessagesSounds()) {
            $pos = $session->getPosition();
            $session->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("note.flute", $pos->x, $pos->y, $pos->z, 1, 1));
        }
    }
}