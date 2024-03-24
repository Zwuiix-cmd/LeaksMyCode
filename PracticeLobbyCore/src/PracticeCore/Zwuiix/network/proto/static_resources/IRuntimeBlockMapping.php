<?php

namespace PracticeCore\Zwuiix\network\proto\static_resources;

interface IRuntimeBlockMapping{

	public function toRuntimeId(int $id, int $meta = 0) : int;

	/**
	 * @param int $runtimeId
	 *
	 * @return int[] [id, meta]
	 */
	public function fromRuntimeId(int $runtimeId) : array;

	public function getBedrockKnownStates() : array;
}