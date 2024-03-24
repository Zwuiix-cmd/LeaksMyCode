<?php

namespace Zwuiix\AdvancedNexus\Task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Zwuiix\AdvancedNexus\Entities\NexusEntity;
use Zwuiix\AdvancedNexus\Handler\NexusHandler;
use Zwuiix\AdvancedNexus\Main;

class NexusTask extends Task
{
    public int $time = 30;

    public Main $plugin;

    public function __construct(Main $main, protected NexusEntity $entity, protected Config $config) {
        $this->plugin=$main;
    }

    public function onRun(): void
    {
        if(!NexusHandler::getInstance()->isNexus()){
            $this->getHandler()->cancel();
            return;
        }

        $max=0;
        $playerMax=null;
        foreach ($this->entity->getNexusCount() as $players => $value){
            if($value > $max){
                $max=$value;
                $playerMax=$players;
            }
        }
        if($playerMax == null){
            return;
        }

        $callable=function (){
            NexusHandler::getInstance()->createBossBar();
            return NexusHandler::getInstance()->getBossBar();
        };
        $bossbar=NexusHandler::getInstance()->getBossBar() ?? $callable();

        $bossbar->setTitle("Vie: " .TextFormat::RED . $this->entity->getHealth());
        $bossbar->setPercentage(($this->entity->getHealth() * 100 / $this->entity->getMaxHealth() / 100));

        $count = $this->entity->getNexusCount();
        $message = "";
        if(count($count) > 0){
            arsort($count);
            $i = 0;
            foreach($count as $name => $value){
                $message .= "\n§e#".($i+1)." §f- §e".$name."§f avec §e".$value." hits§f.\n";
                if($i >= 3) break;
                ++$i;
            }
        }
        $bossbar->setSubTitle($message);

        $user=Server::getInstance()->getPlayerByPrefix($playerMax);
        if(!$user instanceof Player)return;
        $players=Server::getInstance()->getOnlinePlayers();

        $bossbar->removePlayers($bossbar->getPlayers());
        $bossbar->addPlayers($players);

        foreach ($players as $player){
            $player->sendPopup(str_replace(["{PLAYER}", "{MAX}"], [$player->getDisplayName(), $max], $this->config->getNested("message.winner-info")));
        }
    }
}