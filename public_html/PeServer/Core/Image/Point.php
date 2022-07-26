<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \Stringable;
use PeServer\Core\Code;

/**
 * @immutable
 */
class Point implements Stringable
{
	/**
	 * 生成
	 *
	 * @param int $x 横幅。
	 * @phpstan-param UnsignedIntegerAlias $x
	 * @param int $y 高さ。
	 * @phpstan-param UnsignedIntegerAlias $y
	 */
	public function __construct(
		public int $x,
		public int $y
	) {
	}

	public function __toString(): string
	{
		return Code::toString($this, $this->x . ',' . $this->y);
	}
}
