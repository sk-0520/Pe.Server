<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use Closure;
use Generator;
use Iterator;
use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\DataContent;
use PeServer\Core\Serialization\JsonSerializer;

/**
 * イテレータ処理。
 */
class CallbackEventStreamContent extends EventStreamContentBase
{
	/**
	 * 生成。
	 *
	 * @param Closure(): Iterator<EventStreamMessage> $callback
	 */
	public function __construct(private Closure $callback, JsonSerializer $jsonSerializer = null)
	{
		parent::__construct($jsonSerializer);
	}

	#region EventStreamContentBase

	protected function getIterator(): Iterator
	{
		return $this->callback->__invoke();
	}

	#endregion
}
