<?php

namespace PlutooCore\task;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\PiggyFactions;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use MusuiEssentials\handlers\protection\Area;
use MusuiEssentials\MusuiPlayer;
use MusuiEssentials\utils\Cooldown;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class OutpostTask extends Task
{
    use SingletonTrait;

    public int $nextReward = 300;
    public int $currentOutpost = 300;
    public ?string $currentFaction = null;
    public ?string $actualFaction = null;
    private int $spam = 0;
    protected Area $area;
    protected Cooldown $cooldown;

    public function __construct()
    {
        $this->area = new Area("Outpost", Server::getInstance()->getWorldManager()->getDefaultWorld(), new Vector3(-204, 79, -202), new Vector3(-198, 85, -196), true, false, false, false, false, false);
        $this->cooldown = new Cooldown("Outpost");
        self::setInstance($this);
    }

    public function onRun(): void
    {
        $this->nextReward--;

        $uncaptureTime = 60;
        $captureTime = 180;
        $rewardTime = 300;

        $players = Server::getInstance()->getOnlinePlayers();
        $actual = $this->actualFaction;

        $facMgr = PiggyFactions::getInstance()->getFactionsManager();
        $playerMgr = PiggyFactions::getInstance()->getPlayerManager();

        if (1 > count($facMgr->getFactions())) {
            return;
        }

        if (!is_null($actual)) {
            if ($this->currentOutpost > 60) {
                $this->currentOutpost = $uncaptureTime;
            }

            if (is_null($facMgr->getFactionByName($actual))) {
                $this->actualFaction = null;
                $this->currentOutpost = $captureTime;
                return;
            } else if (is_null($this->currentFaction)) {
                foreach ($players as $player) {
                    $playerFaction = $playerMgr->getPlayer($player);
                    if ($playerFaction instanceof FactionsPlayer && $player->isAlive() && $this->area->isInArea($player->getPosition()) && !is_null($playerFaction->getFaction()) && $playerFaction->getFaction()->getName() !== $actual) {
                        $this->currentFaction = $playerFaction->getFaction()->getName();
                        if ((time() - $this->spam) > 2) {
                            Server::getInstance()->broadcastMessage("§5La §9{$this->currentFaction}§5 capture l'§9Outpost§5 de la §9{$actual}§5!");
                            $this->spam = time();
                        }
                        return;
                    }
                }

                $this->currentOutpost = $uncaptureTime;
            } elseif (is_null($facMgr->getFactionByName($this->currentFaction)) || !$this->searchPlayersFaction($this->currentFaction)) {
                $this->currentFaction = null;
                $this->currentOutpost = $uncaptureTime;
                return;
            }

            $this->currentOutpost--;

            if (0 >= $this->currentOutpost) {
                Server::getInstance()->broadcastMessage("§5La §9{$actual}§5 vient de perdre l'§9Outpost§5!");
                $this->actualFaction = null;
                $this->currentOutpost = $captureTime;
            }

            $faction = $facMgr->getFactionByName($actual);
            if (0 >= $this->nextReward && $faction instanceof Faction) {
                $this->nextReward = $rewardTime;
                if(count($faction->getOnlineMembers()) <= 0) return;

                $member = $faction->getOnlineMembers()[array_rand($faction->getOnlineMembers())];
                if($member instanceof MusuiPlayer) {
                    $piggy = \DaPigGuy\PiggyFactions\players\PlayerManager::getInstance()->getPlayer($member);
                    if($piggy instanceof FactionsPlayer) {
                        $piggy->setPower($piggy->getPower() + 4);
                    }
                }

                Server::getInstance()->broadcastMessage("§5La §9{$actual}§5 vient de récupérer des récompenses grâce à l'§9Outpost§5!");
            }
            return;
        }

        if (is_null($this->currentFaction)) {
            foreach ($players as $player) {
                $playerFaction = $playerMgr->getPlayer($player);
                if ($playerFaction instanceof FactionsPlayer && $player->isAlive() && $this->area->isInArea($player->getPosition()) && !is_null($playerFaction->getFaction())) {
                    $this->currentFaction = $playerFaction->getFaction()->getName();
                    if ((time() - $this->spam) > 2) {
                        Server::getInstance()->broadcastMessage("§5La §9{$this->currentFaction}§5 s'empare de l'§9Outpost§5!");
                        $this->spam = time();
                    }
                    return;
                }
            }

            $this->currentOutpost = $captureTime;
        } else {
            if (is_null($facMgr->getFactionByName($this->currentFaction)) || !self::searchPlayersFaction($this->currentFaction)) {
                $this->currentFaction = null;
                $this->currentOutpost = $captureTime;
                return;
            }
        }

        $this->currentOutpost--;

        if (0 >= $this->currentOutpost) {
            Server::getInstance()->broadcastMessage("§5La §9{$this->currentFaction}§5 vient de capturer l'§9Outpost§5!");
            $this->actualFaction = $this->currentFaction;
            $this->currentFaction = null;
            $this->currentOutpost = $uncaptureTime;
            $this->nextReward = $rewardTime;
        }
    }

    /**
     * @param string $faction
     * @return bool
     */
    private function searchPlayersFaction(string $faction): bool
    {
        $facMgr = PiggyFactions::getInstance()->getFactionsManager();
        $faction = $facMgr->getFactionByName($faction);
        if ($faction instanceof Faction) {
            foreach ($faction->getOnlineMembers() as $player) {
                if ($player->isAlive() && $this->area->isInArea($player->getPosition())) {
                    return true;
                }
            }
        }
        return false;
    }
}
