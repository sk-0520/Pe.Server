<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use PeServer\Core\Encoding;
use PeServer\Core\Errors\ErrorHandler;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Stream;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\NotSupportedException;

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
	) {
		parent::__construct($resource);
	}

	#region function

	/**
	 * これだけ使うのです。
	 *
	 * @param string $path
	 * @return self
	 */
	public static function read(string $path): self
	{
		$mode = "rb";
		$result = ErrorHandler::trap(fn() => fopen($path, $mode));
		if ($result->isFailureOrFalse()) {
			throw new IOException($path);
		}

		return new self($path, $result->value);
	}

	#endregion

	#region Stream

	public static function new(string $path, string $mode, ?Encoding $encoding = null): static
	{
		throw new NotSupportedException();
	}

	public static function openStandardInput(?Encoding $encoding = null): self
	{
		throw new NotSupportedException();
	}

	public static function openStandardOutput(?Encoding $encoding = null): self
	{
		throw new NotSupportedException();
	}

	public static function openStandardError(?Encoding $encoding = null): self
	{
		throw new NotSupportedException();
	}

	public static function createTemporaryFile(?Encoding $encoding = null): static
	{
		throw new NotSupportedException();
	}


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
