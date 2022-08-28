<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use \stdClass;
use Directory;
use PeServer\Core\Binary;
use PeServer\Core\Cryptography;
use PeServer\Core\DefaultValue;
use PeServer\Core\Encoding;
use PeServer\Core\Environment;
use PeServer\Core\ErrorHandler;
use PeServer\Core\IO\IOState;
use PeServer\Core\IO\Stream;
use PeServer\Core\Serialization\Json;
use PeServer\Core\ResultData;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\FileNotFoundException;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\ParseException;

/**
 * ファイル+ディレクトリ処理系。
 */
abstract class IOUtility
{
	#region function

	public static function getState(string $path): IOState
	{
		/** @var ResultData<array<string|int,int>|false> */
		$result = ErrorHandler::trapError(fn () => stat($path));
		if (!$result->success) {
			throw new IOException();
		}
		if ($result->value === false) {
			throw new IOException();
		}

		return IOState::createFromStat($result->value);
	}

	/**
	 * ファイル・ディレクトリが存在するか。
	 *
	 * `file_exists` ラッパー。
	 *
	 * @param string $path
	 * @return boolean 存在するか。
	 * @see https://www.php.net/manual/function.file-exists.php
	 */
	public static function existsItem(string $path): bool
	{
		return file_exists($path);
	}

	/**
	 * ファイルのステータスのキャッシュをクリア
	 *
	 * `clearstatcache` ラッパー。
	 *
	 * @param string|null $path
	 * @return void
	 * @see https://www.php.net/manual/function.clearstatcache.php
	 */
	public static function clearCache(?string $path)
	{
		if (is_null($path)) {
			clearstatcache(true);
			return;
		}

		if (Text::isNullOrWhiteSpace($path)) {
			throw new IOException();
		}

		clearstatcache(true, $path);
	}

	#endregion
}
