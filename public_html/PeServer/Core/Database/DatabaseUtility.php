<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Text;
use PeServer\Core\Throws\InvalidOperationException;

abstract class DatabaseUtility
{
	public static function isSqlite(ConnectionSetting $connection): bool
	{
		return Text::startsWith($connection->dsn, 'sqlite', false);
	}

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

	public static function getSqliteFilePath(ConnectionSetting $connection): string
	{
		if (!self::isSqlite($connection)) {
			throw new InvalidOperationException();
		}

		if (self::isSqliteMemoryMode($connection)) {
			throw new InvalidOperationException();
		}

		return $connection->source;
	}

	#endregion
}
