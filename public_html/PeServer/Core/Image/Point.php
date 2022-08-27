<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \Stringable;
use PeServer\Core\Code;

/**
 * 座標。
 */
class Point implements Stringable
{
	private static ?Point $emptyValue = null; //phpstan-ignore-line static

	/**
	 * 生成
	 *
	 * @param int $x X座標。
	 * @param int $y Y座標。
	 */
	public function __construct(
		/** @readonly */
		public int $x,
		/** @readonly */
		public int $y
	) {
	}

	#region function

	public static function empty(): Point
	{
		return self::$emptyValue ??= new Point(0, 0);
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return Code::toString($this, $this->x . ',' . $this->y);
	}

	#endregion
}
