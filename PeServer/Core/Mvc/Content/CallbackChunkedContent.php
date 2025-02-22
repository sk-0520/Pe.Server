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

/**
 * イテレータ処理。
 */
class CallbackChunkedContent extends ChunkedContentBase
{
	/**
	 * 生成。
	 *
	 * @param Closure(): Iterator<Binary> $callback
	 * @param non-empty-string $mime
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 */
	public function __construct(private Closure $callback, string $mime = Mime::STREAM)
	{
		parent::__construct($mime);
	}

	#region ChunkedContentBase

	protected function getIterator(): Iterator
	{
		return $this->callback->__invoke();
	}

	#endregion
}
