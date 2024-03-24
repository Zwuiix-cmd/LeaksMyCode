<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace PracticeCore\Zwuiix\network\proto\latest;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\handler\InGamePacketHandler;
use pocketmine\network\mcpe\InventoryManager;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\player\Player;

/**
 * This handler handles packets related to general gameplay.
 */
class LatestProtocolGameHandler extends InGamePacketHandler {
    private const MAX_FORM_RESPONSE_DEPTH = 2; //modal/simple will be 1, custom forms 2 - they will never contain anything other than string|int|float|bool|null

    /** @var float */
    protected $lastRightClickTime = 0.0;
    /** @var UseItemTransactionData|null */
    protected $lastRightClickData = null;

    protected ?Vector3 $lastPlayerAuthInputPosition = null;
    protected ?float $lastPlayerAuthInputYaw = null;
    protected ?float $lastPlayerAuthInputPitch = null;
    protected ?int $lastPlayerAuthInputFlags = null;

    /** @var bool */
    public $forceMoveSync = false;

    protected ?string $lastRequestedFullSkinId = null;

    public function __construct(
        private readonly Player         $player,
        private readonly NetworkSession $session,
        private readonly InventoryManager $inventoryManager
    ){
        parent::__construct($player, $session, $inventoryManager);
    }

    public function getNetworkSession(): NetworkSession{
        return $this->session;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getInvManager(): InventoryManager{
        return $this->inventoryManager;
    }
}