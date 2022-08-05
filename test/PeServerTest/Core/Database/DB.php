<?php

declare(strict_types=1);

namespace PeServerTest\Core\Database;

use PeServer\Core\Database\Database;
use PeServer\Core\Log\Logging;

/** テスト用DB処理 */
class DB
{
	/**
	 * 各テスト内で使用するメモリDBを取得。
	 *
	 * @return Database
	 */
	public static function memory(): Database
	{
		return new Database('sqlite::memory:', '', '', null, Logging::create(self::class));
	}
}
