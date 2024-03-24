<?php

declare(strict_types=1);

namespace Zwuiix\Libs\muqsit\invmenu\type\graphic\network;

use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use Zwuiix\Libs\muqsit\invmenu\session\InvMenuInfo;
use Zwuiix\Libs\muqsit\invmenu\session\PlayerSession;

interface InvMenuGraphicNetworkTranslator{

	public function translate(PlayerSession $session, InvMenuInfo $current, ContainerOpenPacket $packet) : void;
}