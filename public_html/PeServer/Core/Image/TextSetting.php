<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use PeServer\Core\Alignment;

class TextSetting
{
	/**
	 * 生成。
	 *
	 * @param int $horizontal
	 * @phpstan-param Alignment::HORIZONTAL_* $horizontal
	 * @param int $vertical
	 * @phpstan-param Alignment::VERTICAL_* $vertical
	 * @param string $fontNameOrPath
	 * @param float $angle
	 */
	public function __construct(
		public int $horizontal,
		public int $vertical,
		public string $fontNameOrPath,
		public float $angle,
	) {
	}
}
