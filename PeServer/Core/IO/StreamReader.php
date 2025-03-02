<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use PeServer\Core\Binary;
use PeServer\Core\DisposerBase;
use PeServer\Core\Encoding;
use PeServer\Core\Text;

class StreamReader extends DisposerBase
{
	#region variable

	/**
	 * バッファサイズ。
	 *
	 * @var positive-int
	 */
	public $bufferSize = 1024;

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
	 * EOF か
	 *
	 * @return bool
	 * @see Stream::isEnd()
	 */
	public function isEnd(): bool
	{
		return $this->stream->isEnd();
	}

	/**
	 * 現在のエンコーディングを使用してBOMを読み取る。
	 *
	 * * 現在位置から読み込む点に注意(シーク位置が先頭以外であれば無視される)。
	 * * 読み込まれた場合(エンコーディングがBOMを持っていて合致した場合)はその分読み進められる。
	 *
	 * @return bool BOMが読み込まれたか。
	 */
	public function readBom(): bool
	{
		$this->throwIfDisposed();

		if ($this->stream->getOffset() !== 0) {
			return false;
		}

		$bom = $this->encoding->getByteOrderMark();
		$bomLength = $bom->count();
		if (!$bomLength) {
			return false;
		}

		$readBuffer = $this->stream->readBinary($bomLength);

		if ($bom->isEquals($readBuffer)) {
			return true;
		}

		$this->stream->seek(-$readBuffer->count(), Stream::WHENCE_CURRENT);
		return false;
	}

	/**
	 * 残りのストリームを全て文字列として読み込み。
	 *
	 * エンコーディングにより復元不可の可能性あり。
	 *
	 * @return string
	 */
	public function readStringContents(): string
	{
		$result = $this->stream->readBinaryContents();
		if (!$result->count()) {
			return Text::EMPTY;
		}

		return $this->encoding->toString($result);
	}

	/**
	 * 現在のストリーム位置から1行分のデータを取得。
	 *
	 * * 位置を進めたり戻したりするので操作可能なストリームで処理すること。
	 * * エンコーディングにより復元不可の可能性あり。
	 *
	 * @return string 読み込んだ行。末尾まで読み込み済みでも空文字列となるため isEnd なりで確認をすること。
	 */
	public function readLine(): string
	{
		$this->throwIfDisposed();

		$cr = $this->encoding->getBinary("\r")->raw;
		$lf = $this->encoding->getBinary("\n")->raw;
		$newlineWidth = strlen($cr);

		$startOffset = $this->stream->getOffset();

		$totalCount = 0;
		$totalBuffer = '';

		$findCr = false;
		$findLf = false;
		$hasNewLine = false;

		while (!$this->stream->isEnd()) {
			$binary = $this->stream->readBinary($this->bufferSize);
			$currentLength = $binary->count();
			if (!$currentLength) {
				break;
			}

			$currentBuffer = $binary->raw;
			$currentOffset = 0;

			while ($currentOffset < $currentLength) {
				if (!$findCr) {
					$findCr = !substr_compare($currentBuffer, $cr, $currentOffset, $newlineWidth, false);
					if (!$findCr) {
						$findLf = !substr_compare($currentBuffer, $lf, $currentOffset, $newlineWidth, false);
					}
					$currentOffset += $newlineWidth;
				}
				if ($findLf) {
					$hasNewLine = true;
					break;
				}
				if ($findCr && $currentOffset < $currentLength) {
					$findLf = !substr_compare($currentBuffer, $lf, $currentOffset, $newlineWidth, false);
					if ($findLf) {
						$currentOffset += $newlineWidth;
					}
					$hasNewLine = true;
					break;
				}
			}

			$totalBuffer .= $currentBuffer;
			$totalCount += $currentOffset;

			if ($hasNewLine) {
				break;
			}
		}

		if ($hasNewLine) {
			$dropWidth = 0;
			if ($findCr) {
				$dropWidth += $newlineWidth;
			}
			if ($findLf) {
				$dropWidth += $newlineWidth;
			}
			$this->stream->seek($startOffset + $totalCount, Stream::WHENCE_HEAD);
			$raw = substr($totalBuffer, 0, $totalCount - $dropWidth);
			$str = $this->encoding->toString(new Binary($raw));

			return $str;
		}

		return $this->encoding->toString(new Binary($totalBuffer));
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
