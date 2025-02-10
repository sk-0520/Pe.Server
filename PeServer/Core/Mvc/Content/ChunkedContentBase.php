<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use Iterator;
use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\DataContent;
use PeServer\Core\OutputBuffer;

/**
 * チャンク基底処理。
 */
abstract class ChunkedContentBase extends DataContentBase implements ICallbackContent
{
	/**
	 * 生成。
	 *
	 * @param non-empty-string $mime
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 */
	public function __construct(string $mime)
	{
		parent::__construct(HttpStatus::OK, $mime);
	}

	#region function

	/** @return Iterator<Binary> */
	abstract protected function getIterator(): Iterator;

	#endregion

	#region ICallbackContent

	final public function getLength(): int
	{
		return ICallbackContent::UNKNOWN;
	}

	final public function output(): void
	{
		$iterator = $this->getIterator();
		foreach ($iterator as $binary) {
			echo (string)$binary->count() . "\r\n"
				. $binary->raw . "\r\n";
			if (OutputBuffer::getLevel() <= 1) {
				OutputBuffer::flush();
			}
			flush();
		}

		echo "0\r\n\r\n";
	}

	#endregion
}
