<?php

namespace PracticeCore\Zwuiix\commands\load;

use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\handler\LanguageHandler;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\args\FloatArgument;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use PracticeCore\Zwuiix\libs\CortexPE\Commando\exception\ArgumentOrderException;
use PracticeCore\Zwuiix\PracticeCore;

class SetHeightLimiterCommand extends BaseCommand
{
    public function __construct()
    {
        parent::__construct(PracticeCore::getInstance()->getPlugin(), "setheightlimiter", "Allows you to modify server heightlimiter.");
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("practicecore.command.setheightlimiter");
        $this->registerArgument(0, new FloatArgument("maxheightreduce"));
        $this->registerArgument(1, new FloatArgument("all"));
        $this->registerArgument(2, new FloatArgument("maxdistance"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $x = $args["maxheightreduce"];
        $y = $args["all"];
        $z = $args["maxdistance"];
        KnockbackHandler::getInstance()->setHeightLimiter(new Vector3($x, $y, $z));
        $sender->sendMessage(LanguageHandler::getInstance()->translate("set_heightlimiter", [$x, $y, $z]));
    }
}