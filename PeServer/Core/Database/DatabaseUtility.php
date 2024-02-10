<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * DB処理的な共通処理。
 */
abstract class DatabaseUtility
{
	/**
	 * 接続設定は SQLite か。
	 * @param ConnectionSetting $connection 接続設定。
	 * @return bool SQLite か。
	 */
	public static function isSqlite(ConnectionSetting $connection): bool
	{
		return Text::startsWith($connection->dsn, 'sqlite:', false);
	}

	/**
	 * 接続設定は SQLite のインメモリか。
	 * @param ConnectionSetting $connection 接続設定。
	 * @return bool インメモリか。
	 * @throws InvalidOperationException 接続設定は SQLite ではない。
	 */
	public static function isSqliteMemoryMode(ConnectionSetting $connection): bool
	{
		if (!self::isSqlite($connection)) {
			throw new InvalidOperationException();
		}

		return
			Text::startsWith($connection->source, ':memory:', false)
			||
			(
				Text::contains($connection->source, '?', false)
				&&
				Text::contains($connection->source, 'mode=memory', false)
			);
	}

	/**
	 * 接続設定から SQlite データベースファイルを取得する。
	 * @param ConnectionSetting $connection 接続設定。
	 * @return string データベースファイルパス。
	 * @throws ArgumentException 接続設定は SQLite ではない。
	 * @throws InvalidOperationException ファイルデータベースではない。
	 */
	public static function getSqliteFilePath(ConnectionSetting $connection): string
	{
		if (!self::isSqlite($connection)) {
			throw new ArgumentException();
		}

		if (self::isSqliteMemoryMode($connection)) {
			throw new InvalidOperationException();
		}

		return $connection->source;
	}

	#endregion
}
