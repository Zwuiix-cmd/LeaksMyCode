<?php

namespace Zwuiix\AdvancedPurification\Task;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Zwuiix\AdvancedPurification\Handler\PurificationHandlers;
use Zwuiix\AdvancedPurification\Main;
use onebone\economyapi\EconomyAPI;

class PurificationTask extends Task
{
    protected array $players = array();
    protected Item $item;
    protected int $timeZone;

    public function __construct(
        protected Main $main,
        protected Config $config
    ) {
        $this->timeZone=$this->config->getNested("zone.time")*2;
        $this->item=ItemFactory::getInstance()->get($this->config->getNested("item.id"), $this->config->getNested("item.meta"), $this->config->getNested("item.count"));
    }

    public function onRun(): void
    {
        foreach ($this->players as $name => $time){
            if(!Server::getInstance()->getPlayerByPrefix($name)){
                unset($this->players[$name]);
            }else{
                $player = Server::getInstance()->getPlayerByPrefix($name);
                if(!PurificationHandlers::getInstance()->isInZone($player, $this->config->getNested("zone.position"))){
                    unset($this->players[$name]);
                    $player->sendTip($this->config->getNested("message.exit-zone"));
                }
            }
        }

        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            if(PurificationHandlers::getInstance()->isInZone($player, $this->config->getNested("zone.position")) && $player->getInventory()->contains($this->item)){
                if(!isset($this->players[$player->getName()])){
                    $this->players[$player->getName()] = $this->config->getNested("zone.time");
                    $time=intval($this->timeZone - $this->players[$player->getName()]*2);
                    var_dump($time);
                    $timeIcon=str_repeat(TextFormat::GREEN.$this->config->getNested("message.icon"), $time);
                    if($time >= $this->timeZone){
                        $redTimeIcon="";
                    }else{
                        $redTimeIcon=str_repeat(TextFormat::RED.$this->config->getNested("message.icon"), $this->timeZone - $time);
                    }
                    $player->sendTip($timeIcon . $redTimeIcon);
                    continue;
                }
                $name=$player->getName();
                $this->players[$name] = $this->players[$name]-1;

                if($this->players[$name] === 0){
                    $player->getInventory()->removeItem($this->item);

                    $point=mt_rand($this->config->getNested("zone.min-money"), $this->config->getNested("zone.max-money"));
                    if($player->hasPermission("advancedpurification.boost")) $point = $point*$this->config->getNested("zone.boost");

                    $player->sendTip($this->config->getNested("message.success-tip"));
                    $player->sendMessage(str_replace("{ARGENT}", $point, $this->config->getNested("message.success-chat")));
                    EconomyAPI::getInstance()->addMoney($player, $point);

                    $this->players[$name] = $this->config->getNested("zone.time");
                    continue;
                }
                $time=intval($this->timeZone - $this->players[$name]*2);
                $timeIcon=str_repeat(TextFormat::GREEN.$this->config->getNested("message.icon"), $time);
                if($time >= $this->timeZone){
                    $redTimeIcon="";
                }else{
                    $redTimeIcon=str_repeat(TextFormat::RED.$this->config->getNested("message.icon"), $this->timeZone - $time);
                }
                $player->sendTip($timeIcon . $redTimeIcon);
            }
        }
    }
}