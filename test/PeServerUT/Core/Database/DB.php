<?php

declare(strict_types=1);

namespace PeServerUT\Core\Database;

use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseConnection;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\NullLogger;
use PeServerTest\KeepDatabaseConnection;

/** テスト用DB処理 */
class DB
{
	/**
	 * 各テスト内で使用するメモリDBを取得。
	 *
	 * @return DatabaseContext
	 */
	public static function memory(): DatabaseContext
	{
		return new DatabaseContext(new ConnectionSetting('sqlite::memory:', '', '', null), new NullLogger());
	}

	public static function memoryConnection(): IDatabaseConnection
	{
		return new DatabaseConnection(new ConnectionSetting('sqlite::memory:', '', '', null), LoggerFactory::createNullFactory());
	}

	public static function memoryKeepConnection(): KeepDatabaseConnection
	{
		return new KeepDatabaseConnection();
	}
}
