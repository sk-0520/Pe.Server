<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use \stdClass;
use PeServer\Core\Binary;
use PeServer\Core\Cryptography;
use PeServer\Core\Encoding;
use PeServer\Core\Environment;
use PeServer\Core\ErrorHandler;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\IOState;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Stream;
use PeServer\Core\Serialization\Json;
use PeServer\Core\ResultData;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\FileNotFoundException;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\ParseException;

/**
 * ファイル処理系。
 */
abstract class File
{
	#region function

	/**
	 * ファイルが存在しない場合に空ファイルの作成。
	 *
	 * `touch` ラッパー。
	 *
	 * @param string $path
	 * @return bool
	 * @see https://www.php.net/manual/function.touch.php
	 */
	public static function createEmptyFileIfNotExists(string $path): bool
	{
		return touch($path);
	}

	/**
	 * ファイルサイズを取得。
	 *
	 * @param string $path
	 * @return integer
	 * @return UnsignedIntegerAlias
	 * @see https://www.php.net/manual/function.filesize.php
	 * @throws IOException
	 */
	public static function getFileSize(string $path): int
	{
		/** @phpstan-var ResultData<UnsignedIntegerAlias|false> */
		$result = ErrorHandler::trapError(fn () => filesize($path));
		if (!$result->success || $result->value === false) {
			throw new IOException();
		}

		return $result->value;
	}

	/**
	 * ファイルの内容を取得。
	 *
	 * @param string $path
	 * @return Binary
	 * @see https://www.php.net/manual/function.file-get-contents.php
	 * @throws IOException
	 */
	public static function readContent(string $path): Binary
	{
		$content = file_get_contents($path);
		if ($content === false) {
			throw new IOException($path);
		}

		return new Binary($content);
	}

	/**
	 * 対象ファイルに指定データを書き込み。
	 *
	 * @param string $path
	 * @param Binary $data
	 * @param boolean $append
	 * @return void
	 * @throws IOException
	 */
	private static function saveContent(string $path, Binary $data, bool $append): void
	{
		$flag = $append ? FILE_APPEND : 0;
		$length = file_put_contents($path, $data->getRaw(), LOCK_EX | $flag);
		if ($length === false) {
			throw new IOException($path);
		}
	}

	/**
	 * 書き込み。
	 *
	 * @param string $path
	 * @param Binary $data
	 * @return void
	 * @throws IOException
	 */
	public static function writeContent(string $path, Binary $data): void
	{
		self::saveContent($path, $data, false);
	}

	/**
	 * 追記。
	 *
	 * @param string $path
	 * @param Binary $data
	 * @return void
	 * @throws IOException
	 */
	public static function appendContent(string $path, Binary $data): void
	{
		self::saveContent($path, $data, true);
	}

	/**
	 * JSONとしてファイル読み込み。
	 *
	 * @param string $path パス。
	 * @return array<mixed> 応答JSON。
	 * @param JsonSerializer|null $jsonSerializer JSON処理
	 * @throws IOException
	 * @throws ParseException パース失敗。
	 */
	public static function readJsonFile(string $path, JsonSerializer $jsonSerializer = null): array
	{
		$content = self::readContent($path);

		$jsonSerializer ??= new JsonSerializer();
		/** @var array<mixed> */
		$value = $jsonSerializer->load($content);

		return $value;
	}

	/**
	 * JSONファイルとして出力。
	 *
	 * @param string $path
	 * @param array<mixed>|\stdClass $data
	 * @param JsonSerializer|null $jsonSerializer JSON処理
	 * @return void
	 * @throws IOException
	 * @throws ParseException
	 */
	public static function writeJsonFile(string $path, array|stdClass $data, ?JsonSerializer $jsonSerializer = null): void
	{
		$jsonSerializer ??= new JsonSerializer();
		$value = $jsonSerializer->save($data);

		self::saveContent($path, $value, false);
	}

	/**
	 * ファイルが存在するか。
	 *
	 * self::existsItem より速い。
	 * `file_exists`より`is_file`の方が速いらすぃ
	 *
	 * `is_file` ラッパー。
	 *
	 * @param string $path
	 * @return boolean 存在するか。
	 * @see https://www.php.net/manual/function.is-file.php
	 */
	public static function exists(string $path): bool
	{
		return is_file($path);
	}

	/**
	 * ファイル削除。
	 *
	 * @param string $filePath ファイルパス。
	 * @return boolean
	 * @throws IOException
	 */
	public static function removeFile(string $filePath): bool
	{
		/** @var ResultData<bool> */
		$result = ErrorHandler::trapError(fn () => unlink($filePath));
		if (!$result->success) {
			throw new IOException();
		}

		return $result->value;
	}

	/**
	 * ファイルが存在する場合に削除する。
	 *
	 * @param string $filePath
	 * @return bool
	 */
	public static function removeFileIfExists(string $filePath): bool
	{
		if (!IOUtility::existsItem($filePath)) {
			return false;
		}

		/** @var ResultData<bool> */
		$result = ErrorHandler::trapError(fn () => unlink($filePath));
		if (!$result->success) {
			return false;
		}

		return $result->value;
	}

	/**
	 * 一意なファイルパスを取得。
	 *
	 * `tempnam` ラッパー。
	 *
	 * @param string $directoryPath ファイル名の親ディレクトリ。
	 * @param string $prefix プレフィックス。
	 * @return string
	 * @see https://www.php.net/manual/function.tempnam.php
	 */
	public static function createUniqueFilePath(string $directoryPath, string $prefix): string
	{
		if (Text::isNullOrWhiteSpace($directoryPath)) {
			throw new ArgumentException('$directoryPath');
		}

		$result = tempnam($directoryPath, $prefix);
		if ($result === false) {
			throw new IOException();
		}

		return $result;
	}

	/**
	 * 一時ファイルの取得。
	 *
	 * @param string $prefix
	 * @return string
	 */
	public static function createTemporaryFilePath(string $prefix = ''): string
	{
		if (Text::isNullOrWhiteSpace($prefix)) {
			$prefixLength = PHP_OS === 'Windows' ? 3 : 64;
			$prefix = Cryptography::generateRandomString($prefixLength, Cryptography::FILE_RANDOM_STRING);
		}

		return self::createUniqueFilePath(Directory::getTemporaryDirectory(), $prefix);
	}

	/**
	 * 一時ファイルのストリーム作成。
	 *
	 * メモリ・一時ファイル兼メモリのストリームを使用する場合は、
	 * `Stream::openMemory`, `Stream::openTemporary` を参照のこと。
	 *
	 * `tmpfile` ラッパー。
	 *
	 * @param Encoding|null $encoding
	 * @return Stream
	 * @throws IOException
	 * @see https://www.php.net/manual/function.tmpfile.php
	 */
	public static function createTemporaryFileStream(?Encoding $encoding = null): Stream
	{
		$resource = tmpfile();
		if ($resource === false) {
			throw new IOException();
		}

		return new Stream($resource, $encoding);
	}

	#endregion
}
