<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

final class EventStreamCommentMessage extends EventStreamMessage
{
	/**
	 * 生成。
	 */
	public function __construct(
		string $data,
	) {
		parent::__construct($data);
	}
}
