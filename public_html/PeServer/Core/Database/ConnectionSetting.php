<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Text;

/**
 * DB接続情報。
 */
readonly class ConnectionSetting
{
	#region variable

	public string $driver;
	public string $source;

	#endregion

	/**
	 * 生成。
	 *
	 * @param string $dsn データソース名。
	 * @param string $user ユーザー名。
	 * @param string $password パスワード。
	 * @param array<string,string|int>|null $options オプション。
	 */
	public function __construct(
		public string $dsn,
		public string $user,
		public string $password,
		public ?array $options = null
	) {
		$values = Text::split($dsn, ':', 2);
		$this->driver = $values[0];
		$this->source = $values[1];
	}
}
