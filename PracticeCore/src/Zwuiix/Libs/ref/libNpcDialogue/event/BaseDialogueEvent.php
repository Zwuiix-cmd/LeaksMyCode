<?php

declare(strict_types=1);

namespace Zwuiix\Libs\ref\libNpcDialogue\event;

use pocketmine\event\Event;
use Zwuiix\Libs\ref\libNpcDialogue\NpcDialogue;

abstract class BaseDialogueEvent extends Event{

	public function __construct(protected NpcDialogue $dialogue){ }

	public function getDialogue() : NpcDialogue{
		return $this->dialogue;
	}
}