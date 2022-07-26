<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

/**
 * @immutable
 */
class ImageInformation
{
	/**
	 * 生成
	 *
	 * @param Size $size
	 * @param string $mime
	 * @param int $type
	 * @phpstan-param ImageType::* $type
	 */
	public function __construct(
		public Size $size,
		public string $mime,
		public int $type
	) {
	}
}
