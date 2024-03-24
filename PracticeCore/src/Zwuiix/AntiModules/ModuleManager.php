<?php

namespace Zwuiix\AntiModules;

use JsonException;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use Zwuiix\AntiModules\modules\big\BigClientCacheBlobStatus;
use Zwuiix\AntiModules\modules\big\BigCraftingEvent;
use Zwuiix\AntiModules\modules\big\BigInventoryTransaction;
use Zwuiix\AntiModules\modules\big\BigItemStackRequest;
use Zwuiix\AntiModules\modules\big\BigPlayerAuthInput;
use Zwuiix\AntiModules\modules\big\BigSetActorData;
use Zwuiix\AntiModules\modules\big\BigText;
use Zwuiix\AntiModules\modules\others\AdventureSettings;
use Zwuiix\AntiModules\modules\others\AimB;
use Zwuiix\AntiModules\modules\others\AimC;
use Zwuiix\AntiModules\modules\others\AntiImmobile;
use Zwuiix\AntiModules\modules\others\Bot;
use Zwuiix\AntiModules\modules\others\FakeEffects;
use Zwuiix\AntiModules\modules\others\IncorrectPosition;
use Zwuiix\AntiModules\modules\others\KillAuraA;
use Zwuiix\AntiModules\modules\others\KillAuraB;
use Zwuiix\AntiModules\modules\others\Linux;
use Zwuiix\AntiModules\modules\others\MotionA;
use Zwuiix\AntiModules\modules\others\Packets;
use Zwuiix\AntiModules\modules\others\PrimarineJS;
use Zwuiix\AntiModules\modules\others\SpoofClient;
use Zwuiix\AntiModules\modules\others\Text;
use Zwuiix\AntiModules\modules\others\TimerA;
use Zwuiix\AntiModules\modules\others\TimerB;
use Zwuiix\AntiModules\modules\others\TimerC;
use Zwuiix\AntiModules\modules\others\ToolBox;
use Zwuiix\Handler\AntiCheatHandler;
use Zwuiix\Handler\Sanction;
use Zwuiix\Main;
use Zwuiix\Player\User;
use Zwuiix\Utils\Marcel;

class ModuleManager
{
    use SingletonTrait;

    /*** @var Module[] */
    protected array $modules = array();

    public const MAX_INVENTORY_TRANSACTION = 100;
    public const MAX_BLOCK_ACTION = 100;
    public const MAX_ITEM_INTERACTION = 100;
    public const MAX_METADATA = 130;
    public const MAX_TEXT_PARAMETERS = 100;
    public const MAX_ITEM_STACK_REQUEST = 100; //NOT USE by PMMP4
    public const MAX_CRAFTING_INPUT = 0;
    public const MAX_CRAFTING_OUTPUT = 1;
    public const MAX_HIT_HASHES = 0; //NOT USE by PMMP4
    public const MAX_MISS_HASHES = 0; //NOT USE by PMMP4

    public function __construct(protected Main $plugin)
    {
        self::setInstance($this);

        $this->register(new Linux());
        $this->register(new PrimarineJS());
        $this->register(new SpoofClient());
        $this->register(new Text());
        $this->register(new ToolBox());

        $this->register(new TimerA());
        $this->register(new TimerB());
        $this->register(new TimerC());

        $this->register(new BigClientCacheBlobStatus());
        $this->register(new BigCraftingEvent());
        $this->register(new BigInventoryTransaction());
        $this->register(new BigItemStackRequest());
        $this->register(new BigPlayerAuthInput());
        $this->register(new BigSetActorData());
        $this->register(new BigText());
    }

    /**
     * @param Module $module
     * @return void
     */
    public function register(Module $module): void
    {
        if(isset($this->modules[$module->getName()]))return;
        Server::getInstance()->getLogger()->notice("[MODULE] : Adding {$module->getName()} module");
        $this->modules[$module->getName()]=$module;
    }

    /**
     * @param DataPacketReceiveEvent $event
     * @return void
     * @priority LOWEST
     * @throws JsonException
     */
    public function checkAll(DataPacketReceiveEvent $event): void
    {
        $player = ($networkSession = $event->getOrigin())->getPlayer();
        $packet = $event->getPacket();
        if(!$networkSession->isConnected()) return;
        if(!$player instanceof User)return;
        if(!$player->spawned)return;

        if($packet instanceof PlayerAuthInputPacket) {
            AntiCheatHandler::getInstance()->getTimerHandler()->addPacket($player);
            $player->lastPing=$player->getNetworkSession()->getPing();
        }
        foreach ($this->modules as $name => $module){
            if(!$player->isConnected()) return;
            if($module->detect($player, $packet)) {
                $this->detectedPlayer($event, $player, $module);
                return;
            }
        }
    }

    /**
     * @throws JsonException
     */
    private function detectedPlayer(DataPacketReceiveEvent $event, User $user, Module $module): void
    {
        if(!$user->isOnline())return;

        $cancel=false;
        $hasLog=false;
        $disconnect=false;
        $flagDespawn=false;
        $blockIP=false;
        $ban=false;

        switch ($module->getType()) {
            case "A+":
                $cancel=true;
                $disconnect=true;
                $flagDespawn=true;
                $blockIP=true;
                $ban=true;
                break;
            case "A":
                $cancel=true;
                $disconnect=true;
                $ban=$module->isBannable();
                break;
            case "B":
                $cancel=true;
                $hasLog=$module->hasLog();
                break;
            case "C":
                $hasLog=$module->hasLog();
                break;
        }


        if($cancel) $event->cancel();
        if($hasLog && isset($module->violations[$user->getName()])) {
            $log="";

            if($module instanceof TimerB){
                $log=Marcel::getInstance()->formatViolationInfo(Marcel::TYPE[0]["TimerB"], $user, null, $user->timerViolations, $module->violations[$user->getName()]);
            } elseif($module instanceof TimerC){
                $log=Marcel::getInstance()->formatViolationInfo(Marcel::TYPE[0]["TimerC"], $user, null, $user->timerViolations, $module->violations[$user->getName()]);
            }

            if($log !== "" && $log !== null) Marcel::getInstance()->informStaff($log);
        }

        if($disconnect) $user->disconnect($module->getDetectMessage(), false);
        if($flagDespawn) $user->flagForDespawn();
        if($blockIP) Server::getInstance()->getNetwork()->blockAddress($event->getOrigin()->getIp(), 60);

        if($ban) $user->kick("§cMalheureusement pour vous, vous avez été détecté par l'AntiCheat!");
    }
}