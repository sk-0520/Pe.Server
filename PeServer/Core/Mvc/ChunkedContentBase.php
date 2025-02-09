<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use Iterator;
use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\DataContent;

/**
 * チャンク基底処理。
 */
abstract class ChunkedContentBase implements ICallbackContent
{
	/**
	 * 生成。
	 *
	 * @param non-empty-string $mime
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 */
	public function __construct(public string $mime)
	{
		//NOP
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
			echo (string)$binary->count() . "\r\n";
			echo $binary->raw . "\r\n";
			flush();
		}

		echo "0\r\n";
		echo "\r\n";
	}

	#endregion
}
