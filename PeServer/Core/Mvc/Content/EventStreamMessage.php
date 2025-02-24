<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use DateInterval;

class EventStreamMessage
{
	/**
	 * 生成。
	 *
	 * @param string|array<mixed>|object $data
	 * @param string|null $event
	 * @param string|null $id
	 * @param DateInterval|null $retry
	 */
	public function __construct(
		public string|array|object $data,
		public ?string $event = null,
		public ?string $id = null,
		public ?DateInterval $retry = null
	) {
		//NOP
	}
}
