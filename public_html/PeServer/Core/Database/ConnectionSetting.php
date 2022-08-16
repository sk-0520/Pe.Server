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
	 * @param string $dsn
	 * @param string $user
	 * @param string $password
	 * @param array<mixed>|null $options
	 */
	public function __construct(
		public string $dsn,
		public string $user,
		public string $password,
		public ?array $options = null
	) {
	}
}
