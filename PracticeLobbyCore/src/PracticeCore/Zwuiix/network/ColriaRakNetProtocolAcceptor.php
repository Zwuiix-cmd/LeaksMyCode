<?php

namespace PracticeCore\Zwuiix\network;

use raklib\server\ProtocolAcceptor;

class ColriaRakNetProtocolAcceptor implements ProtocolAcceptor{

	/**
	 * MVProtocolAcceptor constructor.
	 *
	 * @param int[] $versions
	 */
	public function __construct(
		private array $versions
	){

	}

	public function accepts(int $protocolVersion) : bool{
		return in_array($protocolVersion, $this->versions, true);
	}

	public function getPrimaryVersion() : int{
		return max($this->versions);
	}
}