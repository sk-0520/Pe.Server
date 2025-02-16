<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use PeServer\Core\Binary;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Encoding;
use PeServer\Core\Errors\ErrorHandler;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\StreamMetaData;
use PeServer\Core\ResourceBase;
use PeServer\Core\ResultData;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\StreamException;

/**
 * ストリーム。
 *
 * @phpstan-extends ResourceBase<mixed>
 */
class Stream extends ResourceBase
{
	#region define

	/** 読み込みモード。 */
	public const MODE_READ = 1;
	/** 書き込みモード。 */
	public const MODE_WRITE = 2;
	/** 読み書きモード。 */
	public const MODE_EDIT = 3;

	/** シーク位置: 先頭。 */
	public const WHENCE_HEAD = SEEK_SET;
	/** シーク位置: 現在位置。 */
	public const WHENCE_CURRENT = SEEK_CUR;
	/** シーク位置: 末尾(設定値は負数となる)。 */
	public const WHENCE_TAIL = SEEK_END;

	#endregion

	#region variable

	/** 文字列として扱うエンコーディング。(バイナリデータは気にしなくてよい) */
	public readonly Encoding $encoding;

	/**
	 * 改行文字。
	 *
	 * 書き込み時に使用される(読み込み時は頑張る)
	 */
	public string $newLine = PHP_EOL;

	#endregion

	/**
	 * 生成。
	 *
	 * 内部的にしか使わない。
	 *
	 * @param $resource ファイルリソース。
	 * @param Encoding|null $encoding
	 */
	protected function __construct(
		$resource,
		?Encoding $encoding = null
	) {
		parent::__construct($resource);

		$this->encoding = $encoding ?? Encoding::getDefaultEncoding();
	}

	#region function

	/**
	 * `fopen` を使用してファイルストリームを生成。
	 *
	 * 原則以下の処理を使ってればよい。
	 * * `static::create`
	 * * `static::open`
	 * * `static::openOrCreate`
	 * * `static::openStandardInput`
	 * * `static::openStandardOutput`
	 * * `static::openStandardError`
	 * * `static::openMemory`
	 * * `static::openTemporary`
	 *
	 * @param string $path ファイルパス。
	 * @param string $mode `fopen:mode` を参照。
	 * @param Encoding|null $encoding
	 * @return static
	 * @throws IOException
	 * @see https://www.php.net/manual/function.fopen.php
	 */
	public static function new(string $path, string $mode, ?Encoding $encoding = null): static
	{
		if (strpos($mode, 'b', 0) === false) {
			$mode .= 'b';
		}

		$result = ErrorHandler::trap(fn() => fopen($path, $mode));
		if ($result->isFailureOrFalse()) {
			throw new IOException($path);
		}

		// @phpstan-ignore new.static
		return new static($result->value, $encoding);
	}

	/**
	 * 新規ファイルのファイルストリームを生成。
	 *
	 * @param string $path ファイルパス。
	 * @param Encoding|null $encoding
	 * @return static
	 * @throws IOException 既にファイルが存在する。
	 */
	public static function create(string $path, ?Encoding $encoding = null): static
	{
		if (File::exists($path)) {
			throw new IOException($path);
		}

		return static::new($path, 'x+', $encoding);
	}

	/**
	 * 既存ファイルからファイルストリームを生成。
	 *
	 * @param string $path ファイルパス。
	 * @param self::MODE_* $mode `self::MODE_*` を指定。 `self::MODE_CRETE`: ファイルが存在しない場合失敗する。
	 * @param Encoding|null $encoding
	 * @return static
	 * @throws IOException
	 */
	public static function open(string $path, int $mode, ?Encoding $encoding = null): static
	{
		$openMode = match ($mode) {
			self::MODE_READ => 'r',
			self::MODE_WRITE => 'a',
			self::MODE_EDIT => 'r+',
		};

		if ($mode === self::MODE_WRITE || $mode == self::MODE_EDIT) {
			if (!File::exists($path)) {
				throw new IOException($path);
			}
		}

		return static::new($path, $openMode, $encoding);
	}

	/**
	 * 既存ファイルからファイルストリームを生成、既存ファイルが存在しない場合新規ファイルを作成してファイルストリームを生成。
	 *
	 * @param string $path ファイルパス。
	 * @param int $mode `self::MODE_*` を指定。 `self::MODE_CRETE`: ファイルが存在しない場合に空ファイルが作成される(読むしかできないけど開ける。意味があるかは知らん)。
	 * @phpstan-param self::MODE_* $mode
	 * @param Encoding|null $encoding
	 * @return static
	 * @throws IOException
	 */
	public static function openOrCreate(string $path, int $mode, ?Encoding $encoding = null): static
	{
		$openMode = match ($mode) {
			self::MODE_READ => 'r',
			self::MODE_WRITE => 'a',
			self::MODE_EDIT => 'r+',
		};

		if ($mode === self::MODE_READ) {
			if (!File::exists($path)) {
				File::createEmptyFileIfNotExists($path);
			}
		}

		return static::new($path, $openMode, $encoding);
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
	 * @return static
	 */
	public static function openMemory(?Encoding $encoding = null): static
	{
		return static::new('php://memory', 'r+', $encoding);
	}

	/**
	 * 一時メモリストリームを開く。
	 *
	 * @param positive-int|null $memoryByteSize 指定した値を超過した際に一時ファイルに置き換わる。`null`の場合は 2MB(`php://temp` 参照のこと)。
	 * @param Encoding|null $encoding
	 * @return static
	 */
	public static function openTemporary(?int $memoryByteSize = null, ?Encoding $encoding = null): static
	{
		$path = 'php://temp';

		if ($memoryByteSize !== null) {
			// [DOCTYPE]
			// @phpstan-ignore smaller.alwaysFalse
			if ($memoryByteSize < 0) {
				throw new ArgumentException('$byteSize: ' . $memoryByteSize);
			}
			//cspell:disable-next-line
			$path .= '/maxmemory:' . (string)$memoryByteSize;
		}

		return static::new($path, 'r+', $encoding);
	}

	/**
	 * 一時ファイルのストリーム作成。
	 *
	 * メモリ・一時ファイル兼メモリのストリームを使用する場合は、
	 * `self::openMemory`, `self::openTemporary` を参照のこと。
	 *
	 * `tmpfile` ラッパー。
	 *
	 * @param Encoding|null $encoding
	 * @return static
	 * @throws IOException
	 * @see https://www.php.net/manual/function.tmpfile.php
	 */
	public static function createTemporaryFile(?Encoding $encoding = null): static
	{
		$resource = tmpfile();
		if ($resource === false) {
			throw new IOException();
		}

		// @phpstan-ignore new.static
		return new static($resource, $encoding);
	}

	public function getState(): IOState
	{
		$this->throwIfDisposed();

		$result = ErrorHandler::trap(fn() => fstat($this->resource));
		if ($result->isFailureOrFalse()) {
			throw new IOException();
		}

		return IOState::createFromStat($result->value);
	}

	public function getMetaData(): StreamMetaData
	{
		$this->throwIfDisposed();

		$values = stream_get_meta_data($this->resource);
		return StreamMetaData::createFromStream($values);
	}

	/**
	 * 現在位置をシーク。
	 *
	 * @param int $offset
	 * @param int $whence
	 * @phpstan-param self::WHENCE_* $whence
	 * @return bool
	 * @see https://www.php.net/manual/function.fseek.php
	 */
	public function seek(int $offset, int $whence): bool
	{
		$this->throwIfDisposed();

		$result = fseek($this->resource, $offset, $whence);
		return $result === 0;
	}

	/**
	 * 先頭へシーク。
	 *
	 * @return bool
	 * @see https://www.php.net/manual/function.rewind.php
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
		return $this->seek(0, self::WHENCE_TAIL);
	}

	/**
	 * 現在位置を取得。
	 *
	 * @return int
	 * @return-param non-negative-int
	 * @throws StreamException
	 * @see https://www.php.net/manual/function.ftell.php
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
	public function isEnd(): bool
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
	 * @phpstan-param non-negative-int|null $byteSize
	 * @return int 書き込んだバイトサイズ。
	 * @phpstan-return non-negative-int
	 * @throws StreamException
	 * @see https://www.php.net/manual/function.fwrite.php
	 */
	public function writeBinary(Binary $data, ?int $byteSize = null): int
	{
		$this->throwIfDisposed();

		$result = ErrorHandler::trap(fn() => fwrite($this->resource, $data->raw, $byteSize));
		if ($result->isFailureOrFalse()) {
			throw new StreamException();
		}

		return $result->value;
	}

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

		if ($this->getOffset() !== 0) {
			return 0;
		}

		$bom = $this->encoding->getByteOrderMark();
		if ($bom->count()) {
			return $this->writeBinary($bom);
		}

		return 0;
	}

	/**
	 * 文字列書き込み。
	 *
	 * @param string $data データ。
	 * @param int|null $count 文字数。
	 * @phpstan-param non-negative-int|null $count
	 * @return int 書き込まれたバイト数。
	 * @phpstan-return non-negative-int
	 */
	public function writeString(string $data, ?int $count = null): int
	{
		$this->throwIfDisposed();

		if (!Text::getByteCount($data)) {
			return 0;
		}

		if ($count === null) {
			return $this->writeBinary($this->encoding->getBinary($data));
		}

		if ($count === 0) {
			return 0;
		}

		$dataLength = Text::getLength($data);
		if ($dataLength <= $count) {
			return $this->writeBinary($this->encoding->getBinary($data));
		}

		$s = Text::substring($data, 0, $count);
		return $this->writeBinary($this->encoding->getBinary($s));
	}

	/**
	 * 文字列を改行付きで書き込み。
	 *
	 * @param string $data
	 * @return int 書き込まれたバイト数。
	 * @phpstan-return non-negative-int
	 */
	public function writeLine(string $data): int
	{
		return $this->writeString($data . $this->newLine);
	}

	/**
	 * バイナリ読み込み。
	 *
	 * @param int $byteSize 読み込みバイトサイズ。
	 * @phpstan-param positive-int $byteSize
	 * @return Binary 読み込んだデータ。
	 * @throws StreamException
	 * @see https://www.php.net/manual/function.fread.php
	 */
	public function readBinary(int $byteSize): Binary
	{
		$this->throwIfDisposed();

		$result = ErrorHandler::trap(fn() => fread($this->resource, $byteSize));
		if ($result->isFailureOrFalse()) {
			throw new StreamException();
		}

		return new Binary($result->value);
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

		if ($this->getOffset() !== 0) {
			return false;
		}

		$bom = $this->encoding->getByteOrderMark();
		$bomLength = $bom->count();
		if (!$bomLength) {
			return false;
		}

		$readBuffer = $this->readBinary($bomLength);

		if ($bom->isEquals($readBuffer)) {
			return true;
		}

		$this->seek(-$readBuffer->count(), self::WHENCE_CURRENT);
		return false;
	}

	/**
	 * 残りのストリームを全てバイナリとして読み込み。
	 *
	 * @param int|null $byteSize 読み込む最大バイト数。`null`で全て。
	 * @phpstan-param non-negative-int|null $byteSize
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
	 * @param int $bufferByteSize 1回読み進めるにあたり予め取得するサイズ。このサイズが改行(CR,LF)のサイズより小さい場合は改行(CR,LF)のサイズが設定される(1指定のutf16とか)
	 * @phpstan-param positive-int $bufferByteSize
	 * @return string
	 */
	public function readLine(int $bufferByteSize = 1024): string
	{
		$this->throwIfDisposed();

		if ($bufferByteSize < 1) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$bufferByteSize');
		}

		$cr = $this->encoding->getBinary("\r")->raw;
		$lf = $this->encoding->getBinary("\n")->raw;
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

		while (!$this->isEnd()) {
			$binary = $this->readBinary($bufferByteSize);
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
			$this->seek($startOffset + $totalCount, self::WHENCE_HEAD);
			$raw = substr($totalBuffer, 0, $totalCount - $dropWidth);
			$str = $this->encoding->toString(new Binary($raw));

			return $str;
		}

		return $this->encoding->toString(new Binary($totalBuffer));
	}

	#endregion

	#region ResourceBase

	protected function release(): void
	{
		fclose($this->resource);
	}

	protected function isValidType(string $resourceType): bool
	{
		return $resourceType === 'stream';
	}

	#endregion
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
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
		//NOP
	}
}
