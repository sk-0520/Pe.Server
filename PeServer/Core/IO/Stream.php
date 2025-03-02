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

	/**
	 * 生成。
	 *
	 * 内部的にしか使わない。
	 *
	 * @param $resource ファイルリソース。
	 */
	protected function __construct(
		$resource
	) {
		parent::__construct($resource);
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
		return new static($result->value);
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

		return static::new($path, 'x+');
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

		return static::new($path, $openMode);
	}

	/**
	 * 既存ファイルからファイルストリームを生成、既存ファイルが存在しない場合新規ファイルを作成してファイルストリームを生成。
	 *
	 * @param string $path ファイルパス。
	 * @param int $mode `self::MODE_*` を指定。 `self::MODE_CRETE`: ファイルが存在しない場合に空ファイルが作成される(読むしかできないけど開ける。意味があるかは知らん)。
	 * @phpstan-param self::MODE_* $mode
	 * @return static
	 * @throws IOException
	 */
	public static function openOrCreate(string $path, int $mode): static
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

		return static::new($path, $openMode);
	}

	/**
	 * 標準エラーストリームを開く。
	 *
	 * @return self
	 */
	public static function openStandardInput(): self
	{
		return new LocalNoReleaseStream(STDIN);
	}
	/**
	 * 標準出力ストリームを開く。
	 *
	 * @return self
	 */
	public static function openStandardOutput(): self
	{
		return new LocalNoReleaseStream(STDOUT);
	}
	/**
	 * 標準エラーストリームを開く。
	 *
	 * @return self
	 */
	public static function openStandardError(): self
	{
		return new LocalNoReleaseStream(STDERR);
	}

	/**
	 * メモリストリームを開く。
	 *
	 * @return static
	 */
	public static function openMemory(): static
	{
		return static::new('php://memory', 'r+');
	}

	/**
	 * 一時メモリストリームを開く。
	 *
	 * @param positive-int|null $memoryByteSize 指定した値を超過した際に一時ファイルに置き換わる。`null`の場合は 2MB(`php://temp` 参照のこと)。
	 * @return static
	 */
	public static function openTemporary(?int $memoryByteSize = null): static
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

		return static::new($path, 'r+');
	}

	/**
	 * 一時ファイルのストリーム作成。
	 *
	 * メモリ・一時ファイル兼メモリのストリームを使用する場合は、
	 * `self::openMemory`, `self::openTemporary` を参照のこと。
	 *
	 * `tmpfile` ラッパー。
	 *
	 * @return static
	 * @throws IOException
	 * @see https://www.php.net/manual/function.tmpfile.php
	 */
	public static function createTemporaryFile(): static
	{
		$resource = tmpfile();
		if ($resource === false) {
			throw new IOException();
		}

		// @phpstan-ignore new.static
		return new static($resource);
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
	 */
	public function __construct(
		$resource
	) {
		parent::__construct($resource);
	}

	protected function release(): void
	{
		//NOP
	}
}
