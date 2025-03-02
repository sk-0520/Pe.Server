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
