<?php

namespace AdvancedPrivateChest\Zwuiix\utils;

use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\InvMenu;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\InvMenuType;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\type\util\InvMenuTypeBuilders;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\inventory\BlockInventory;
use pocketmine\block\inventory\BlockInventoryTrait;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\SimpleInventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\world\Position;

final class DropperInventory implements InvMenuType
{
    private InvMenuType $inner;

    public function __construct()
    {
        $this->inner = InvMenuTypeBuilders::BLOCK_ACTOR_FIXED()
            ->setBlock(BlockFactory::getInstance()->get(BlockLegacyIds::DROPPER, 0))
            ->setSize(9)
            ->setBlockActorId("Dropper")
            ->setNetworkWindowType(WindowTypes::DROPPER)
            ->build();
    }

    /**
     * @param InvMenu $menu
     * @param Player $player
     * @return InvMenuGraphic|null
     */
    public function createGraphic(InvMenu $menu, Player $player): ?InvMenuGraphic
    {
        return $this->inner->createGraphic($menu, $player);
    }

    public function createInventory(): Inventory
    {
        return new DropperInventoryPMMP(Position::fromObject(Vector3::zero(), null));
    }
}

class DropperInventoryPMMP extends SimpleInventory implements BlockInventory{
    use BlockInventoryTrait;

    public function __construct(Position $holder, int $size = 9){
        $this->holder = $holder;
        parent::__construct($size);
    }
}
