<?php

namespace MusuiAntiCheat\Zwuiix\handler;

use JsonException;
use MusuiAntiCheat\Zwuiix\Main;
use MusuiAntiCheat\Zwuiix\session\Session;
use MusuiAntiCheat\Zwuiix\utils\Data;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class BanHandler
{
    use SingletonTrait;

    protected array $tempData = [];

    public function __construct(
        protected Data $data,
    )
    {
        self::setInstance($this);
        $this->tempData = $this->data->getAll();
    }

    /**
     * @param Session $session
     * @return bool
     */
    public function isBanned(string $name): bool
    {
        return isset($this->tempData[strtolower($name)]);
    }

    /**
     * @param Session $session
     * @return string|null
     */
    public function getBannedUser(Session $session): ?string
    {
        foreach (AliasesHandler::getInstance()->syncAllAlt($session) as $check){
            if(isset($this->tempData[strtolower($check)])) {
                return $check;
            }
        }

        return null;
    }

    /**
     * @param Session $session
     * @param string $staff
     * @param string $reason
     * @param string $details
     * @param bool $manual
     * @return void
     * @throws JsonException
     */
    public function ban(Session $session, string $staff, string $reason, string $details): void
    {
        if($this->isBanned($session->getPlayerName())) return;
        $this->tempData[strtolower($session->getPlayer()->getName())] = [
            "staff" => $staff,
            "reason" => $reason,
            "details" => $details
        ];

        Server::getInstance()->broadcastMessage(LanguageHandler::getInstance()->translate("session_banned", [$session->getPlayer()->getName(), $reason, $staff, $details]));
        $this->canConnect($session);
    }

    /**
     * @param string $name
     * @return void
     * @throws JsonException
     */
    public function unban(string $name): void
    {
        if(!$this->isBanned($name)) return;
        unset($this->tempData[strtolower($name)]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getReason(string $name): mixed
    {
        return $this->tempData[strtolower($name)]["reason"] ?? "Unknown";
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getStaff(string $name): mixed
    {
        return $this->tempData[strtolower($name)]["staff"] ?? "Unknown";
    }

    public function canConnect(Session $session): void
    {
        if(!$this->isBanned($session->getPlayerName())) return;
        if(!$session->getPlayer()->isConnected() or !$session->getPlayer()->spawned) return;

        $name = $this->getBannedUser($session);
        $reason = $this->getReason($name);
        $staff = $this->getStaff($name);

        $message = LanguageHandler::getInstance()->translate("banned_message", [$reason, $staff]);
        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use($session, $message) {
            if(!$session->getPlayer()->isConnected()) return;
            $session->getPlayer()->kick($message, false);
        }), 1);
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function save(): void
    {
        $this->data->setAll($this->tempData);
        $this->data->save();
    }
}