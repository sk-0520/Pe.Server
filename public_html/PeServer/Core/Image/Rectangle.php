<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \Serializable;
use \Stringable;
use PeServer\Core\Code;

/**
 * 矩形領域。
 *
 * @immutable
 */
class Rectangle implements Stringable, Serializable
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

	public function serialize(): string
	{
		$values = [
			'point' => $this->point,
			'size' => $this->size,
		];

		return serialize($values);
	}

	public function unserialize(string $data): void
	{
		$values = unserialize($data);

		$this->point = $values['point']; //@phpstan-ignore-line Serializable
		$this->size = $values['size']; //@phpstan-ignore-line Serializable
	}

	public function __toString(): string
	{
		return Code::toString($this, $this->point . ',' . $this->size);
	}
}
