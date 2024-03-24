<?php

namespace PracticeCore\Zwuiix\scoreboard;

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use PracticeCore\Zwuiix\session\Session;

class Scoreboard
{
    /*** @var string[] */
    public array $lines = [];

    /**
     * @param NetworkSession $networkSession
     * @param string $objectiveName
     * @param string $displayName
     * @param int $sortOrder
     * @return void
     */
    protected function sendScoreboardPacket(NetworkSession $networkSession, string $objectiveName, string $displayName, int $sortOrder = 0): void
    {
        $networkSession->sendDataPacket(SetDisplayObjectivePacket::create('sidebar', $objectiveName, $displayName, 'dummy', $sortOrder));
    }

    /**
     * @param NetworkSession $networkSession
     * @param string $objectiveName
     * @return void
     */
    protected function removeScoreboardPacket(NetworkSession $networkSession, string $objectiveName): void
    {
        $networkSession->sendDataPacket(RemoveObjectivePacket::create($objectiveName));
    }

    /**
     * @param NetworkSession $networkSession
     * @param string $objectiveName
     * @param int $actorUniqueId
     * @param int $id
     * @param string $line
     * @return void
     */
    protected function sendSetScorePacket(NetworkSession $networkSession, string $objectiveName, int $actorUniqueId, int $id, string $line): void
    {
        if(isset($this->lines[$actorUniqueId][$id])){
            $this->removeSetScorePacket($networkSession, $id);
            unset($this->lines[$actorUniqueId][$id]);
        }

        $packet = new ScorePacketEntry();
        $packet->score = $id;
        $packet->objectiveName = $objectiveName;
        $packet->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        $packet->customName = $line;
        $packet->scoreboardId = $id;
        $packet->actorUniqueId = $actorUniqueId;
        $this->lines[$actorUniqueId][$id] = $packet;

        $networkSession->sendDataPacket(SetScorePacket::create(SetScorePacket::TYPE_CHANGE, [$packet]));
    }

    /**
     * @param NetworkSession $networkSession
     * @param int $id
     * @return void
     */
    protected function removeSetScorePacket(NetworkSession $networkSession, int $id): void
    {
        $pk = new  SetScorePacket();
        $pk->type = SetScorePacket::TYPE_REMOVE;
        $pk->entries[] = $this->lines[$networkSession->getPlayer()->getId()][$id];
        $networkSession->sendDataPacket($pk);
    }

    /**
     * @param Session $session
     * @param string $name
     * @param array $lines
     * @return void
     */
    public function sendScoreboard(Session $session, string $name, array $lines): void
    {
        for($i = 0; $i < count($lines); $i++) $this->addLine($session, $i, $lines[$i]);
        $this->sendScoreboardPacket($session->getNetworkSession(), $session->getName(), $name);
    }

    /**
     * @param Session $session
     * @return void
     */
    public function removeScoreboard(Session $session): void
    {
        $this->removeScoreboardPacket($session->getNetworkSession(), $session->getName());
    }

    /**
     * @param Session $session
     * @param int $id
     * @param string $line
     * @return void
     */
    public function addLine(Session $session, int $id, string $line): void
    {
        $this->sendSetScorePacket($session->getNetworkSession(), $session->getName(), $session->getId(), $id, $line);
    }
}