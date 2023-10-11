<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use PeServer\Core\ErrorHandler;
use PeServer\Core\IO\IOState;
use PeServer\Core\ResultData;
use PeServer\Core\Text;
use PeServer\Core\Throws\IOException;

/**
 * ファイル+ディレクトリ処理系。
 */
abstract class IOUtility
{
	#region function

	public static function getState(string $path): IOState
	{
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
		if ($path === null) {
			clearstatcache(true);
			return;
		}

		if (Text::isNullOrWhiteSpace($path)) {
			throw new IOException();
		}

		clearstatcache(true, $path);
	}


	/**
	 * ファイル移動。
	 *
	 * @param string $fromPath
	 * @param string $toPath
	 * @return bool
	 * @see https://www.php.net/manual/function.rename.php
	 */
	public static function move(string $fromPath, string $toPath): bool
	{
		return \rename($fromPath, $toPath);
	}


	#endregion
}
