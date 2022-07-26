<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \Stringable;
use PeServer\Core\Code;

/**
 * @immutable
 */
class Rectangle implements Stringable
{
	/**
	 * 生成。
	 *
	 * @param Point $point
	 * @param Size $size
	 */
	public function __construct(
		public Point $point,
		public Size $size
	) {
	}

	public function left(): int
	{
		return $this->point->x;
	}
	public function top(): int
	{
		return $this->point->y;
	}

	public function right(): int
	{
		return $this->point->x + $this->size->width;
	}
	public function bottom(): int
	{
		return $this->point->x + $this->size->height;
	}

	public function __toString(): string
	{
		return Code::toString($this, $this->point . ',' . $this->size);
	}
}
