<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use Stringable;
use PeServer\Core\Code;

/**
 * 座標。
 */
class Point implements Stringable
{
	#region variable

	private static ?Point $emptyValue = null; //phpstan-ignore-line static

	#endregion

	/**
	 * 生成
	 *
	 * @param int $x X座標。
	 * @param int $y Y座標。
	 */
	public function __construct(
		public readonly int $x,
		public readonly int $y
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
		return Code::toString($this, ['x', 'y']);
	}

	#endregion
}
