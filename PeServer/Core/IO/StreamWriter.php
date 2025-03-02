<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use PeServer\Core\DisposerBase;
use PeServer\Core\Encoding;
use PeServer\Core\Text;

class StreamWriter extends DisposerBase
{
	#region variable

	public string $newLine = PHP_EOL;

	#endregion

	public function __construct(
		protected Stream $stream,
		protected Encoding $encoding,
		private bool $leaveOpen = false
	) {
		//NOP
	}

	#region function

	/**
	 * 現在のエンコーディングを使用してBOMを書き込み。
	 *
	 * * 現在位置に書き込む点に注意(シーク位置が先頭以外であれば無視される)。
	 * * エンコーディングがBOM情報を持っていれば出力されるためBOM不要な場合は使用しないこと。
	 *
	 * @return int 書き込まれたバイトサイズ。
	 * @phpstan-return non-negative-int
	 */
	public function writeBom(): int
	{
		$this->throwIfDisposed();

		if ($this->stream->getOffset() !== 0) {
			return 0;
		}

		$bom = $this->encoding->getByteOrderMark();
		if ($bom->count()) {
			return $this->stream->writeBinary($bom);
		}

		return 0;
	}

	/**
	 * 文字列書き込み。
	 *
	 * @param string $s データ。
	 * @return int 書き込まれたバイト数。
	 * @phpstan-return non-negative-int
	 */
	public function writeString(string $s): int
	{
		$this->throwIfDisposed();

		if (!Text::getByteCount($s)) {
			return 0;
		}

		$data = $this->encoding->getBinary($s);
		return $this->stream->writeBinary($data);
	}

	/**
	 * 文字列を改行付きで書き込み。
	 *
	 * @param string $s
	 * @return int 書き込まれたバイト数。
	 * @phpstan-return non-negative-int
	 */
	public function writeLine(string $s): int
	{
		return $this->writeString($s . $this->newLine);
	}

	#endregion

	#region DisposerBase

	protected function disposeImpl(): void
	{
		if ($this->leaveOpen) {
			return;
		}

		$this->stream->dispose();
	}

	#endregion
}
