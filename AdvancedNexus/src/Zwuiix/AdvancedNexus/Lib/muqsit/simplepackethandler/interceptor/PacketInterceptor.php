<?php

declare(strict_types=1);

namespace Zwuiix\AdvancedNexus\Lib\muqsit\simplepackethandler\interceptor;

use Zwuiix\AdvancedNexus\Lib\muqsit\simplepackethandler\interceptor\IPacketInterceptor;
use Zwuiix\AdvancedNexus\Lib\muqsit\simplepackethandler\interceptor\PacketInterceptorListener;
use Closure;
use pocketmine\plugin\Plugin;

final class PacketInterceptor implements IPacketInterceptor{

	private PacketInterceptorListener $listener;

	public function __construct(Plugin $register, int $priority, bool $handle_cancelled){
		$this->listener = new PacketInterceptorListener($register, $priority, $handle_cancelled);
	}

	public function interceptIncoming(Closure $handler) : IPacketInterceptor{
		$this->listener->interceptIncoming($handler);
		return $this;
	}

	public function interceptOutgoing(Closure $handler) : IPacketInterceptor{
		$this->listener->interceptOutgoing($handler);
		return $this;
	}

	public function unregisterIncomingInterceptor(Closure $handler) : IPacketInterceptor{
		$this->listener->unregisterIncomingInterceptor($handler);
		return $this;
	}

	public function unregisterOutgoingInterceptor(Closure $handler) : IPacketInterceptor{
		$this->listener->unregisterOutgoingInterceptor($handler);
		return $this;
	}
}