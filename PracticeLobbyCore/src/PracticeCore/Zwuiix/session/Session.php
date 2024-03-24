<?php

namespace PracticeCore\Zwuiix\session;

use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\player\PlayerInfo;
use pocketmine\Server;
use PracticeCore\Zwuiix\ffa\FFA;
use PracticeCore\Zwuiix\handler\KitHandler;
use PracticeCore\Zwuiix\handler\KnockbackHandler;
use PracticeCore\Zwuiix\replay\Record;
use PracticeCore\Zwuiix\scoreboard\Scoreboard;

class Session extends Player {}