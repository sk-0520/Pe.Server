<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

class EventStreamMessage
{
	/**
	 * 生成。
	 *
	 * NOTE: コメントは別に。。。
	 *
	 * @param string|array<mixed>|object $data
	 * @param string|null $event
	 * @param string|null $id
	 * @param int|null $retr
	 */
	public function __construct(
		public string|array|object $data,
		public ?string $event = null,
		public ?string $id = null,
		public ?int $retr = null
	) {
		//NOP
	}
}
