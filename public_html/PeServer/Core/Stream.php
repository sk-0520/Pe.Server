<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\ErrorHandler;
use PeServer\Core\InitialValue;
use PeServer\Core\IOUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\StreamException;

/**
 * @phpstan-extends ResourceBase<mixed>
 */
class Stream extends ResourceBase
{
	/** 読み込みモード。 */
	public const MODE_READ = 1;
	/** 書き込みモード。 */
	public const MODE_WRITE = 2;
	/** 読み書きモード。 */
	public const MODE_EDIT = 3;

	private Encoding $encoding;

	/**
	 * 改行文字。
	 *
	 * 書き込み時に使用される(読み込み時は頑張る)
	 */
	public string $newLine = PHP_EOL;

	/**
	 * 生成
	 *
	 * @param $resource ファイルリソース。
	 * @param Encoding|null $encoding
	 */
	public function __construct(
		$resource,
		?Encoding $encoding = null
	) {
		parent::__construct($resource);

		$this->encoding = $encoding ?? Encoding::getDefaultEncoding();
	}

	/**
	 * `fopen` を使用してファイルストリームを生成。
	 *
	 * @param string $path ファイルパス。
	 * @param string $mode `fopen:mode` を参照。
	 * @param Encoding|null $encoding
	 * @return self
	 * @throws IOException
	 * @see https://www.php.net/manual/function.fopen.php
	 */
	public static function new(string $path, string $mode, ?Encoding $encoding = null): self
	{
		if (strpos($mode, 'b', 0) === false) {
			$mode .= 'b';
		}

		$result = ErrorHandler::trapError(fn () => fopen($path, $mode));
		if (!$result->success) {
			throw new IOException($path);
		}
		if ($result->value === false) {
			throw new StreamException($path);
		}

		return new self($result->value, $encoding);
	}

	/**
	 * 新規ファイルのファイルストリームを生成。
	 *
	 * @param string $path ファイルパス。
	 * @param Encoding|null $encoding
	 * @return self
	 * @throws IOException 既にファイルが存在する。
	 */
	public static function create(string $path, ?Encoding $encoding = null): self
	{
		if (IOUtility::existsFile($path)) {
			throw new IOException($path);
		}

		return self::new($path, 'x+', $encoding);
	}

	/**
	 * 既存ファイルからファイルストリームを生成。
	 *
	 * @param string $path ファイルパス。
	 * @param int $mode `self::MODE_*` を指定。 `self::MODE_CRETE`: ファイルが存在しない場合失敗する。
	 * @phpstan-param self::MODE_* $mode
	 * @param Encoding|null $encoding
	 * @return self
	 * @throws IOException
	 */
	public static function open(string $path, int $mode, ?Encoding $encoding = null): self
	{
		$openMode = match ($mode) {
			self::MODE_READ => 'r',
			self::MODE_WRITE => 'a',
			self::MODE_EDIT => 'r+', //@phpstan-ignore-line
			default => throw new ArgumentException('$mode: ' . $mode), //@phpstan-ignore-line
		};

		if ($mode === self::MODE_WRITE || $mode == self::MODE_EDIT) {
			if (!IOUtility::existsFile($path)) {
				throw new IOException($path);
			}
		}

		return self::new($path, $openMode, $encoding);
	}

	/**
	 * 既存ファイルからファイルストリームを生成、既存ファイルが存在しない場合新規ファイルを作成してファイルストリームを生成。
	 *
	 * @param string $path ファイルパス。
	 * @param int $mode `self::MODE_*` を指定。 `self::MODE_CRETE`: ファイルが存在しない場合に空ファイルが作成される(読むしかできないけど開ける。意味があるかは知らん)。
	 * @phpstan-param self::MODE_* $mode
	 * @param Encoding|null $encoding
	 * @return self
	 * @throws IOException
	 */
	public static function openOrCreate(string $path, int $mode, ?Encoding $encoding = null): self
	{
		$openMode = match ($mode) {
			self::MODE_READ => 'r',
			self::MODE_WRITE => 'a',
			self::MODE_EDIT => 'r+', //@phpstan-ignore-line
			default => throw new ArgumentException('$mode: ' . $mode), //@phpstan-ignore-line
		};

		if ($mode === self::MODE_READ) {
			if (!IOUtility::existsFile($path)) {
				IOUtility::createEmptyFileIfNotExists($path);
			}
		}

		return self::new($path, $openMode, $encoding);
	}

	/**
	 * 標準エラーストリームを開く。
	 *
	 * @param Encoding|null $encoding
	 * @return self
	 */
	public static function openStandardInput(?Encoding $encoding = null): self
	{
		return new LocalNoReleaseStream(STDIN, $encoding);
	}
	/**
	 * 標準出力ストリームを開く。
	 *
	 * @param Encoding|null $encoding
	 * @return self
	 */
	public static function openStandardOutput(?Encoding $encoding = null): self
	{
		return new LocalNoReleaseStream(STDOUT, $encoding);
	}
	/**
	 * 標準エラーストリームを開く。
	 *
	 * @param Encoding|null $encoding
	 * @return self
	 */
	public static function openStandardError(?Encoding $encoding = null): self
	{
		return new LocalNoReleaseStream(STDERR, $encoding);
	}

	/**
	 * メモリストリームを開く。
	 *
	 * @param Encoding|null $encoding
	 * @return self
	 */
	public static function openMemory(?Encoding $encoding = null): self
	{
		return self::new('php://memory', 'r+', $encoding);
	}

	/**
	 * 一時メモリストリームを開く。
	 *
	 * @param int|null $memoryByteSize 指定した値を超過した際に一時ファイルに置き換わる。`null`の場合は 2MB(`php://temp` 参照のこと)。
	 * @param Encoding|null $encoding
	 * @return self
	 */
	public static function openTemporary(?int $memoryByteSize = null, ?Encoding $encoding = null): self
	{
		$path = 'php://temp';

		if (!is_null($memoryByteSize)) {
			if ($memoryByteSize < 0) {
				throw new ArgumentException('$byteSize: ' . $memoryByteSize);
			}
			if ($memoryByteSize) {
				$path .= ':' . (string)$memoryByteSize;
			}
		}

		return self::new($path, 'r+', $encoding);
	}

	/**
	 * 現在地をシーク。
	 *
	 * @param int $offset
	 * @param int $whence
	 * @phpstan-param SEEK_SET|SEEK_END|SEEK_CUR $whence
	 * @return bool
	 * @see https://php.net/manual/function.fseek.php
	 */
	public function seek(int $offset, int $whence = SEEK_SET): bool
	{
		$this->throwIfDisposed();

		$result = fseek($this->resource, $offset, $whence);
		return $result === 0;
	}

	/**
	 * 先頭へシーク。
	 *
	 * @return bool
	 * @see https://php.net/manual/function.rewind.php
	 */
	public function seekHead(): bool
	{
		$this->throwIfDisposed();

		return rewind($this->resource);
	}

	/**
	 * 末尾へシーク。
	 *
	 * @return bool
	 */
	public function seekTail(): bool
	{
		return $this->seek(0, SEEK_END);
	}

	/**
	 * 現在地を取得。
	 *
	 * @return int
	 * @return-param UnsignedIntegerAlias
	 * @throws StreamException
	 * @see https://php.net/manual/function.ftell.php
	 */
	public function getOffset(): int
	{
		$this->throwIfDisposed();

		$result = ftell($this->resource);
		if ($result === false) {
			throw new StreamException();
		}
		return $result;
	}

	/**
	 * EOFか。
	 *
	 * `feof` ラッパー。
	 *
	 * @return bool
	 * @see https://www.php.net/manual/function.feof.php
	 */
	public function eof(): bool
	{
		if ($this->isDisposed()) {
			return false;
		}

		return feof($this->resource);
	}

	/**
	 * フラッシュ。
	 *
	 * @throws StreamException
	 * @see https://www.php.net/manual/function.fflush.php
	 */
	public function flush(): void
	{
		$this->throwIfDisposed();

		$result = fflush($this->resource);
		if (!$result) {
			throw new StreamException();
		}
	}

	/**
	 * バイナリ書き込み。
	 *
	 * @param Binary $data データ。
	 * @param int|null $byteSize 書き込みサイズ。
	 * @phpstan-param UnsignedIntegerAlias|null $byteSize
	 * @return int 書き込んだバイトサイズ。
	 * @phpstan-return UnsignedIntegerAlias
	 * @throws StreamException
	 * @see https://php.net/manual/function.fwrite.php
	 */
	public function writeBinary(Binary $data, ?int $byteSize = null): int
	{
		$this->throwIfDisposed();

		$result = fwrite($this->resource, $data->getRaw(), $byteSize);
		if ($result === false) {
			throw new StreamException();
		}

		return $result;
	}

	/**
	 * 現在のエンコーディングを使用してBOMを書き込み。
	 *
	 * * 現在地に書き込む点に注意。
	 * * エンコーディングがBOM情報を持っていれば出力されるためBOM不要な場合は使用しないこと。
	 *
	 * @return int 書き込まれたバイトサイズ。
	 */
	public function writeBom(): int
	{
		$this->throwIfDisposed();

		$bom = $this->encoding->getByteOrderMark();
		if ($bom->getLength()) {
			return $this->writeBinary($bom);
		}

		return 0;
	}

	/**
	 * 文字列書き込み。
	 *
	 * @param string $data データ。
	 * @param int|null $count 文字数。
	 * @phpstan-param UnsignedIntegerAlias|null $count
	 * @return int 書き込まれたバイト数。
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function writeString(string $data, ?int $count = null): int
	{
		$this->throwIfDisposed();

		if (!StringUtility::getByteCount($data)) {
			return 0;
		}

		if (is_null($count)) {
			return $this->writeBinary($this->encoding->getBinary($data));
		}

		if ($count === 0) {
			return 0;
		}

		$dataLength = StringUtility::getLength($data);
		if ($dataLength <= $count) {
			return $this->writeBinary($this->encoding->getBinary($data));
		}

		$s = StringUtility::substring($data, 0, $count);
		return $this->writeBinary($this->encoding->getBinary($s));
	}

	/**
	 * 文字列を改行付きで書き込み。
	 *
	 * @param string $data
	 * @return int 書き込まれたバイト数。
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function writeLine(string $data): int
	{
		return $this->writeString($data . $this->newLine);
	}

	/**
	 * バイナリ読み込み。
	 *
	 * @param int $byteSize 読み込みバイトサイズ。
	 * @phpstan-param UnsignedIntegerAlias $byteSize
	 * @return Binary 読み込んだデータ。
	 * @throws StreamException
	 * @see https://php.net/manual/function.fread.php
	 */
	public function readBinary(int $byteSize): Binary
	{
		$this->throwIfDisposed();

		$result = fread($this->resource, $byteSize);
		if ($result === false) {
			throw new StreamException();
		}

		return new Binary($result);
	}

	/**
	 * 現在のエンコーディングを使用してBOMを読み取る。
	 *
	 * * 現在地から読み込む点に注意。
	 * * 読み込まれた場合(エンコーディングがBOMを持っていて合致した場合)はその分読み進められる。
	 *
	 * @return bool BOMが読み込まれたか。
	 */
	public function readBom(): bool
	{
		$bom = $this->encoding->getByteOrderMark();
		$bomLength = $bom->getLength();
		if (!$bomLength) {
			return false;
		}

		$readBuffer = $this->readBinary($bomLength);

		if ($bom->isEquals($readBuffer)) {
			return true;
		}

		$this->seek(-$readBuffer->getLength(), SEEK_CUR);
		return false;
	}

	/**
	 * 残りのストリームを全てバイナリとして読み込み。
	 *
	 * @param int|null $byteSize 読み込む最大バイト数。`null`で全て。
	 * @phpstan-param UnsignedIntegerAlias|null $byteSize
	 * @param int $offset 読み込みを開始する前に移動する位置。
	 * @return Binary
	 * @throws StreamException
	 * @see https://www.php.net/manual/function.stream-get-contents.php
	 */
	public function readBinaryContents(?int $byteSize = null, int $offset = -1): Binary
	{
		$this->throwIfDisposed();

		$result = stream_get_contents($this->resource, $byteSize, $offset);
		if ($result === false) {
			throw new StreamException();
		}

		return new Binary($result);
	}

	/**
	 * 残りのストリームを全て文字列として読み込み。
	 *
	 * エンコーディングにより復元不可の可能性あり。
	 *
	 * @return string
	 * @throws StreamException
	 */
	public function readStringContents(): string
	{
		$result = $this->readBinaryContents();
		if (!$result->getLength()) {
			return InitialValue::EMPTY_STRING;
		}

		return $this->encoding->toString($result);
	}

	/**
	 * 現在のストリーム位置から1行分のデータを取得。
	 *
	 * * 位置を進めたり戻したりするので操作可能なストリームで処理すること。
	 * * エンコーディングにより復元不可の可能性あり。
	 *
	 * @param int $bufferByteSize 1回読み進めるにあたり予め取得するサイズ。このサイズが改行(CR,LF)のサイズより小さい場合は改行(CR,LF)のサイズが設定される(1指定のutf16とか)
	 * @phpstan-param positive-int $bufferByteSize
	 * @return string
	 */
	public function readLine(int $bufferByteSize = 1024): string
	{
		$this->throwIfDisposed();

		if ($bufferByteSize < 1) { //@phpstan-ignore-line positive-int
			throw new ArgumentException('$bufferByteSize');
		}

		$cr = $this->encoding->getBinary("\r")->getRaw();
		$lf = $this->encoding->getBinary("\n")->getRaw();
		$newlineWidth = strlen($cr);

		if ($bufferByteSize < $newlineWidth) {
			$bufferByteSize = $newlineWidth;
		}

		$startOffset = $this->getOffset();

		$totalCount = 0;
		$totalBuffer = '';

		$findCr = false;
		$findLf = false;
		$hasNewLine = false;

		while (!$this->eof()) {
			$binary = $this->readBinary($bufferByteSize);
			$currentLength = $binary->getLength();
			if (!$currentLength) {
				break;
			}

			$currentBuffer = $binary->getRaw();
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
			$this->seek($startOffset + $totalCount, SEEK_SET);
			$raw = substr($totalBuffer, 0, $totalCount - $dropWidth);
			$str = $this->encoding->toString(new Binary($raw));

			return $str;
		}

		return $this->encoding->toString(new Binary($totalBuffer));
	}


	//ResourceBase--------------------------------

	protected function release(): void
	{
		fclose($this->resource);
	}

	protected function isValidType(string $resourceType): bool
	{
		return $resourceType === 'stream';
	}
}

class LocalNoReleaseStream extends Stream
{
	/**
	 * 生成
	 *
	 * @param $resource ファイルリソース。
	 * @param Encoding|null $encoding
	 */
	public function __construct(
		$resource,
		?Encoding $encoding = null
	) {
		parent::__construct($resource, $encoding);
	}

	protected function release(): void
	{
		//NONE
	}
}
