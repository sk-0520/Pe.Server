<?php

declare(strict_types=1);

namespace PeServer\Core\Database\Management;

use PeServer\Core\TypeUtility;

/**
 * カラム型。
 */
readonly class DatabaseColumnType
{
	/**
	 * 生成
	 *
	 * @param string $rawType
	 * @param int $precision
	 * @param int $scale
	 * @param string $phpType
	 * @phpstan-param TypeUtility::TYPE_* $phpType
	 *
	 */
	public function __construct(
		public string $rawType,
		public int $precision,
		public int $scale,
		public string $phpType
	) {
		//NOP
	}
}
