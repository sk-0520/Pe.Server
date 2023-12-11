<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use PeServer\Core\Image\HorizontalAlignment;
use PeServer\Core\Image\VerticalAlignment;

class TextSetting
{
	/**
	 * 生成。
	 *
	 * @param HorizontalAlignment $horizontal
	 * @param VerticalAlignment $vertical
	 * @param string $fontNameOrPath
	 * @param float $angle
	 * @codeCoverageIgnore
	 */
	public function __construct(
		public HorizontalAlignment $horizontal,
		public VerticalAlignment $vertical,
		public string $fontNameOrPath,
		public float $angle,
	) {
	}
}
