<?php

namespace PracticeCore\Zwuiix\network\proto;

use pocketmine\network\mcpe\protocol\serializer\ItemTypeDictionary;
use PracticeCore\Zwuiix\network\proto\static_resources\IRuntimeBlockMapping;

class MVPacketSerializerContext implements IMVPacketSerializerContext{

	public function __construct(
		private ItemTypeDictionary $itemDictionary,
		private IRuntimeBlockMapping $blockMapping
	){

	}

	public function getItemDictionary() : ItemTypeDictionary{
		return $this->itemDictionary;
	}

	public function getBlockMapping() : IRuntimeBlockMapping{
		return $this->blockMapping;
	}
}