<?php

declare(strict_types=1);

namespace PeServer\Core\Setup;

use Attribute;

/**
 * セットアップ処理で使用されるバージョン情報。
 */
#[Attribute(Attribute::TARGET_CLASS)]
readonly class MigrationVersion
{
	/**
	 * 生成。
	 *
	 * @param int $version セットアップバージョン。
	 */
	public function __construct(
		public int $version
	) {
		//NOP
	}
}
