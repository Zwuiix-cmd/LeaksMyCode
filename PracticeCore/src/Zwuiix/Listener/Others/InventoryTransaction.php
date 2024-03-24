<?php

namespace Zwuiix\Listener\Others;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\ArmorInventory;

class InventoryTransaction implements Listener
{
    public function onTransaction(InventoryTransactionEvent $event)
    {
        $player=$event->getTransaction()->getSource();
        foreach ($event->getTransaction()->getInventories() as $inventory){
            if($inventory instanceof ArmorInventory) $event->cancel();
        }
    }
}