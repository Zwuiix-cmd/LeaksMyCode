<?php

declare(strict_types=1);

namespace Zwuiix\Libs\muqsit\invmenu\type\util;

use Zwuiix\Libs\muqsit\invmenu\type\util\builder\ActorFixedInvMenuTypeBuilder;
use Zwuiix\Libs\muqsit\invmenu\type\util\builder\BlockActorFixedInvMenuTypeBuilder;
use Zwuiix\Libs\muqsit\invmenu\type\util\builder\BlockFixedInvMenuTypeBuilder;
use Zwuiix\Libs\muqsit\invmenu\type\util\builder\DoublePairableBlockActorFixedInvMenuTypeBuilder;

final class InvMenuTypeBuilders{

	public static function ACTOR_FIXED() : ActorFixedInvMenuTypeBuilder{
		return new ActorFixedInvMenuTypeBuilder();
	}

	public static function BLOCK_ACTOR_FIXED() : BlockActorFixedInvMenuTypeBuilder{
		return new BlockActorFixedInvMenuTypeBuilder();
	}

	public static function BLOCK_FIXED() : BlockFixedInvMenuTypeBuilder{
		return new BlockFixedInvMenuTypeBuilder();
	}

	public static function DOUBLE_PAIRABLE_BLOCK_ACTOR_FIXED() : DoublePairableBlockActorFixedInvMenuTypeBuilder{
		return new DoublePairableBlockActorFixedInvMenuTypeBuilder();
	}
}