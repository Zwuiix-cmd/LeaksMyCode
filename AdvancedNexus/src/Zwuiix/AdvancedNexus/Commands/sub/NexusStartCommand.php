<?php

namespace Zwuiix\AdvancedNexus\Commands\sub;

use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use Zwuiix\AdvancedNexus\Entities\NexusEntity;
use Zwuiix\AdvancedNexus\Handler\NexusHandler;
use Zwuiix\AdvancedNexus\Lib\CortexPE\Commando\BaseSubCommand;
use Zwuiix\AdvancedNexus\Main;
use Zwuiix\AdvancedNexus\Task\NexusTask;

class NexusStartCommand extends BaseSubCommand
{
    protected function prepare(): void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(NexusHandler::getInstance()->isNexus()){
            $sender->sendMessage(TextFormat::RED."Désolée, un nexus est déjà en cours...");
            return;
        }

        NexusHandler::getInstance()->setNexus(true);

        $config=Main::getInstance()->getData();
        $location=new Location($config->getNested("entity.position.x"), $config->getNested("entity.position.y"), $config->getNested("entity.position.z"), Server::getInstance()->getWorldManager()->getWorldByName($config->getNested("entity.position.world")), 0, 0);

        $entity=new NexusEntity($location, $config);
        $entity->spawnToAll();

        Server::getInstance()->broadcastMessage($config->getNested("message.broadcast-announce"));
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new NexusTask(Main::getInstance(), $entity, $config), 20);
    }
}