<?php

namespace PlutooCore\task;

use MusuiEssentials\handlers\faction\FactionHandler;
use MusuiEssentials\handlers\protection\Area;
use MusuiEssentials\handlers\ServerHandler;
use MusuiEssentials\MusuiPlayer;
use PlutooCore\handlers\event\Event;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class KothTask extends Task
{
    use SingletonTrait;

    public int $time = 0;
    public array $players = [];
    public string $name = "Aucun";
    public int $captureTime = 45;
    public Area $area;

    public function __construct(
        protected Event $event
    ) {
        $this->area = new Area("Koth", Server::getInstance()->getWorldManager()->getDefaultWorld(), new Vector3(-90, 74, 255), new Vector3(-84, 71, 261), true, false, false, false, false, false);
        self::setInstance($this);
    }

    /**
     * @return void
     */
    public function onRun(): void
    {
        if ($this->name !== "Aucun"){
            $player = ServerHandler::getInstance()->getPlayerExact($this->name);
            if (!$player instanceof MusuiPlayer){
                $this->captureTime = 45;
                $this->name = "Aucun";
                return;
            }

            $this->captureTime--;
            Server::getInstance()->broadcastTip("§9- §1{$player->getDisplayName()} §acapture le koth §7({$this->captureTime}/45) §9-");
            if ($this->captureTime <= 0) {
                $this->finish($player);
                $this->getHandler()->cancel();
                return;
            }
        }
        $this->time++;
    }

    /**
     * @param MusuiPlayer $player
     * @return void
     */
    public function finish(MusuiPlayer $player) : void
    {
        $this->players=[];

        $this->captureTime = 45;
        $this->name = "Aucun";
        $this->event->stop();


        $faction = FactionHandler::getInstance()->getExtension()->getFaction($player);
        $msg = $faction !== "§7..." ? TextFormat::GRAY . "Le joueur §9{$player->getName()}§7 de la faction §9{$faction}§7 vient de remporté le §1Koth §7!" : TextFormat::GRAY . "Le joueur §9{$player->getName()}§7 vient de remporté le §1Koth §7!";
        Server::getInstance()->broadcastMessage($msg);
        $player->sendMessage("§aBien joué ! Tu as gagner le §9Koth§a, vous avez reçu les récompenses.");
        $inventory = $player->getInventory();
        foreach (
            [
                VanillaItems::GOLDEN_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4)),
                VanillaItems::GOLDEN_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4)),
                VanillaItems::GOLDEN_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4)),
                VanillaItems::GOLDEN_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 4)),
                VanillaItems::GOLDEN_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 5)),
                VanillaBlocks::SPONGE()->asItem()->setCount(8),
            ] as $item) {
            if ($inventory->canAddItem($item)) {
                $inventory->addItem($item);
            } else $player->getWorld()->dropItem($player->getPosition(), $item);
        }
    }

    /**
     * @param MusuiPlayer $player
     * @return void
     */
    public function setKing(MusuiPlayer $player) : void
    {
        $this->name = $player->getName();
        $this->captureTime = 45;
    }

    /**
     * @return string
     */
    public function getKingFaction(): string
    {
        if($this->name === "Aucun") return "Aucune";

        $player = Server::getInstance()->getPlayerExact($this->name);
        if(!$player instanceof MusuiPlayer) return "Aucune";

        $faction = FactionHandler::getInstance()->getExtension()->getFaction($player);
        return $faction === "§7..." ? "Aucune" : $faction;
    }

    /**
     * @param MusuiPlayer $player
     * @return void
     */
    public function syncUser(MusuiPlayer $player) : void
    {
        if (!isset($this->players[$player->getName()])) {
            $this->players[$player->getName()] = $player->getName();
            if($this->name === "Aucun") $this->setKing($player);
        }
    }

    /**
     * @param MusuiPlayer $player
     * @return void
     */
    public function removeUser(MusuiPlayer $player): void
    {
        if (isset($this->players[$player->getName()])) {
            unset($this->players[$player->getName()]);
            if($this->name === $player->getName()) {
                $this->name = "Aucun";
                $this->captureTime = 45;
            }
        }
    }
}
