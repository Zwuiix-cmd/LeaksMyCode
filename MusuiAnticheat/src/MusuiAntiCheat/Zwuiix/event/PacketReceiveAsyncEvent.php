<?php

namespace MusuiAntiCheat\Zwuiix\event;

use MusuiAntiCheat\Zwuiix\libs\ColinHDev\libAsyncEvent\AsyncEvent;
use MusuiAntiCheat\Zwuiix\libs\ColinHDev\libAsyncEvent\ConsecutiveEventHandlerExecutionTrait;
use pocketmine\event\server\DataPacketReceiveEvent;

class PacketReceiveAsyncEvent extends DataPacketReceiveEvent implements AsyncEvent
{
    use ConsecutiveEventHandlerExecutionTrait;
}