<?php

declare(strict_types=1);

namespace PracticeCore\Zwuiix\libs\muqsit\invmenu\type\graphic\network;

use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use PracticeCore\Zwuiix\libs\muqsit\invmenu\session\InvMenuInfo;
use PracticeCore\Zwuiix\libs\muqsit\invmenu\session\PlayerSession;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}