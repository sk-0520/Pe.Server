<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \Serializable;
use \Stringable;
use PeServer\Core\Code;

/**
 * 座標。
 */
class Point implements Stringable, Serializable
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

	public static function empty(): Point
	{
		return self::$emptyValue ??= new Point(0, 0);
	}

	public function serialize(): string
	{
		$values = [
			'x' => $this->x,
			'y' => $this->y,
		];

		return serialize($values);
	}

	public function unserialize(string $data): void
	{
		$values = unserialize($data);

		$this->x = $values['x']; //@phpstan-ignore-line Serializable
		$this->y = $values['y']; //@phpstan-ignore-line Serializable
	}

	public function __toString(): string
	{
		return Code::toString($this, $this->x . ',' . $this->y);
	}
}
