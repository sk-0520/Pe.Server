<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

/**
 * DB設定。
 *
 * @immutable
 */
class DatabaseSetting
{
	#region variable

	public string $connection;
	public string $user;
	public string $password;

	#endregion
}
