<?php

namespace PlutooCore\task;

use muqsit\customsizedinvmenu\CustomSizedInvMenu;
use MusuiEssentials\libs\muqsit\invmenu\InvMenu;
use MusuiEssentials\utils\PacketUtils;
use PlutooCore\block\tile\CrateTile;
use PlutooCore\handlers\crate\CrateItem;
use PlutooCore\player\CustomMusuiPlayer;
use pocketmine\scheduler\Task;

class CrateRouletteTask extends Task
{
    const INVENTORY_ROW_COUNT = 9;

    private int $currentTick = 0;
    private bool $showReward = false;
    private InvMenu $menu;
    private int $itemsLeft;

    /** @var CrateItem[] */
    private array $lastRewards = [];

    public function __construct(
        protected CrateTile $tile,
        protected CustomMusuiPlayer $session
    ) {

        $this->menu = CustomSizedInvMenu::create(9);
        $this->menu->setName($this->tile->getCrateName());
        $this->menu->setListener(InvMenu::readonly());
        $this->menu->send($this->session);

        $this->itemsLeft = round(count($this->tile->getLoots()) / (mt_rand(1, 3)));
    }

    public function onRun(): void
    {
        if (!$this->session->isOnline()) {
            $this->tile->closeCrate();
            if (($handler = $this->getHandler()) !== null) $handler->cancel();
            return;
        }

        $this->currentTick++;
        $speed = 0.7;
        $safeSpeed = max($speed, 1);
        $duration = 30;
        $safeDuration = (($duration / $safeSpeed) >= 5.5) ? $duration : (5.5 * $safeSpeed);

        if ($this->currentTick >= $safeDuration) {
            if (!$this->showReward) {
                $this->showReward = true;
            } elseif ($this->currentTick - $safeDuration > 20) {
                $this->itemsLeft--;
                $reward = $this->lastRewards[floor(self::INVENTORY_ROW_COUNT / 2)];

                $this->session->removeCurrentWindow();
                $this->tile->closeCrate();
                if (($handler = $this->getHandler()) !== null) $handler->cancel();
                PacketUtils::playSound($this->session->getPosition(), [$this->session], "note.flute");
                $item = $reward->getItem()->setLore([]);
                if($this->session->getInventory()->canAddItem($item)) {
                    $this->session->getInventory()->addItem($reward->getItem()->setLore([]));
                } else $this->session->getWorld()->dropItem($this->session->getPosition(), $item);
                return;
            }
            return;
        }

        if ($this->currentTick % $safeSpeed === 0) {
            $this->lastRewards[self::INVENTORY_ROW_COUNT] = $this->tile->getDrop(1)[0];
            /**
             * @var int $slot
             * @var CrateItem $lastReward
             */
            foreach ($this->lastRewards as $slot => $lastReward) {
                if ($slot !== 0) {
                    $this->lastRewards[$slot - 1] = $lastReward;
                    $this->menu->getInventory()->setItem($slot - 1, $lastReward->getItem());
                    PacketUtils::playSound($this->session->getPosition(), [$this->session], "random.click");
                }
            }
        }
    }
}
