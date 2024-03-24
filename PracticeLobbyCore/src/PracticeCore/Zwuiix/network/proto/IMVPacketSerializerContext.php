<?php

namespace PracticeCore\Zwuiix\network\proto;

use pocketmine\network\mcpe\protocol\serializer\ItemTypeDictionary;
use PracticeCore\Zwuiix\network\proto\static_resources\IRuntimeBlockMapping;

interface IMVPacketSerializerContext{

	public function getItemDictionary() : ItemTypeDictionary;

	public function getBlockMapping() : IRuntimeBlockMapping;
}