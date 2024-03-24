<?php

declare(strict_types=1);

namespace MusuiAntiCheat\Zwuiix\libs\muqsit\asynciterator;

use Iterator;
use MusuiAntiCheat\Zwuiix\libs\muqsit\asynciterator\handler\AsyncForeachHandler;
use MusuiAntiCheat\Zwuiix\libs\muqsit\asynciterator\handler\SimpleAsyncForeachHandler;
use pocketmine\scheduler\TaskScheduler;

class AsyncIterator{

	public function __construct(
		readonly private TaskScheduler $scheduler
	){}

	/**
	 * @template TKey
	 * @template TValue
	 * @param Iterator<TKey, TValue> $iterable
	 * @param int $entries_per_tick
	 * @param int $sleep_time
	 * @return AsyncForeachHandler<TKey, TValue>
	 */
	public function forEach(Iterator $iterable, int $entries_per_tick = 10, int $sleep_time = 1) : AsyncForeachHandler{
		$handler = new SimpleAsyncForeachHandler($iterable, $entries_per_tick);
		$task_handler = $this->scheduler->scheduleDelayedRepeatingTask(new AsyncForeachTask($handler), 1, $sleep_time);
		$handler->init("Plugin: {$task_handler->getOwnerName()} Event: AsyncIterator");
		return $handler;
	}
}