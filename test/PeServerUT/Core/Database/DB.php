<?php

declare(strict_types=1);

namespace PeServerUT\Core\Database;

use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\NullLogger;

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
}
