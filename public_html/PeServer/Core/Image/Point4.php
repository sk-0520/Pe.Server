<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \Stringable;
use PeServer\Core\Code;
use PeServer\Core\StringUtility;

/**
 * @immutable
 */
class Point4 implements Stringable
{
	public function __construct(
		public Point $leftBottom,
		public Point $rightBottom,
		public Point $rightTop,
		public Point $leftTop
	) {
	}

	public function __toString(): string
	{
		return Code::toString($this, StringUtility::dump($this));
	}
}
