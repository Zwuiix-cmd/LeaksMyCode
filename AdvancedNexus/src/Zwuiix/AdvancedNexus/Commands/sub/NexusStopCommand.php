<?php

namespace Zwuiix\AdvancedNexus\Commands\sub;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Zwuiix\AdvancedNexus\Entities\NexusEntity;
use Zwuiix\AdvancedNexus\Handler\NexusHandler;
use Zwuiix\AdvancedNexus\Lib\CortexPE\Commando\BaseSubCommand;
use Zwuiix\AdvancedNexus\Main;

class NexusStopCommand extends BaseSubCommand
{
    protected function prepare(): void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!NexusHandler::getInstance()->isNexus()){
            $sender->sendMessage(TextFormat::RED."Désolée, il n'y a aucun nexus en cours...");
            return;
        }
        foreach (Server::getInstance()->getWorldManager()->getDefaultWorld()->getEntities() as $entity){
            if($entity instanceof NexusEntity){
                $entity->flagForDespawn();
            }
        }
        NexusHandler::getInstance()->setNexus(false);
        Server::getInstance()->broadcastMessage(Main::getInstance()->getData()->getNested("message.broadcast-stop"));
    }
}