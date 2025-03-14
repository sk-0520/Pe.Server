<?php

declare(strict_types=1);

namespace PeServer\Core\Database\Management;

/**
 * データベース情報。
 */
readonly class DatabaseInformationItem
{
	/**
	 * 生成
	 *
	 * @param string $name データベース名。
	 */
	public function __construct(
		public string $name
	) {
		//NOP
	}
}
