<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use PeServer\Core\Encoding;
use PeServer\Core\Errors\ErrorHandler;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Stream;
use PeServer\Core\Throws\IOException;

/**
 * 処理終了後に該当ファイルを削除するストリーム。
 *
 * HTTPの応答に使用してそのまま消えていくような一時的ファイルを対象とし、読み込み専用で処理する想定。
 *
 * 全て読み込まれた場合に削除されるので中途半端な読み込み状態は残るためどこかしらでお掃除は必要となる。
 */
final class FileCleanupStream extends Stream
{
	/**
	 * 生成。
	 */
	private function __construct(
		public readonly string $path,
		$resource,
		?Encoding $encoding = null
	) {
		parent::__construct($resource, $encoding);
	}

	#region function

	/**
	 * これだけ使うのです。
	 *
	 * @param string $path
	 * @param Encoding|null $encoding
	 * @return self
	 */
	public static function read(string $path, ?Encoding $encoding = null): self
	{
		$mode = "rb";
		$result = ErrorHandler::trap(fn() => fopen($path, $mode));
		if ($result->isFailureOrFalse()) {
			throw new IOException($path);
		}

		return new self($path, $result->value, $encoding);
	}

	#endregion

	#region Stream

	protected function disposeImpl(): void
	{
		$canRemove = $this->isEnd();

		parent::disposeImpl();

		if ($canRemove) {
			File::removeFileIfExists($this->path);
		}
	}

	#endregion
}
