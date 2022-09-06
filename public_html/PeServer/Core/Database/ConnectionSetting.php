<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

/**
 * DB接続情報。
 */
class ConnectionSetting
{
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
	}
}
