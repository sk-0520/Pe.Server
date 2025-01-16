<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use Stringable;
use PeServer\Core\Code;

/**
 * 矩形領域。
 */
readonly class Rectangle implements Stringable
{
	/**
	 * 生成。
	 *
	 * @param Point $point 開始座標。
	 * @param Size $size 開始座標からの幅と高さ。
	 */
	public function __construct(
		public Point $point,
		public Size $size
	) {
	}

	#region function

	/**
	 *
	 * @return int
	 * @phpstan-pure
	 */
	public function left(): int
	{
		return $this->point->x;
	}
	/**
	 *
	 * @return int
	 * @phpstan-pure
	 */
	public function top(): int
	{
		return $this->point->y;
	}

	/**
	 *
	 * @return int
	 * @phpstan-pure
	 */
	public function right(): int
	{
		return $this->point->x + $this->size->width;
	}
	/**
	 *
	 * @return int
	 * @phpstan-pure
	 */
	public function bottom(): int
	{
		return $this->point->y + $this->size->height;
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return Code::toString($this, ['point', 'size']);
	}

	#endregion
}
