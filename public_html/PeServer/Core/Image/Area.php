<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \Stringable;
use PeServer\Core\Code;
use PeServer\Core\Image\Point;
use PeServer\Core\Text;

/**
 * 上下左右の座標を保持。
 *
 * @immutable
 */
class Area implements Stringable
{
	/**
	 * 生成
	 *
	 * @param Point $leftTop 左上座標([6], [7])
	 * @param Point $leftBottom 左下座標([0], [1])
	 * @param Point $rightBottom 右下座標([2], [3])
	 * @param Point $rightTop 右上座標([4], [5])
	 */
	public function __construct(
		public Point $leftTop,
		public Point $leftBottom,
		public Point $rightBottom,
		public Point $rightTop
	) {
	}

	#region function

	/**
	 * 配列から生成。
	 *
	 * @param int[] $areaArray
	 * @phpstan-param non-empty-array<int> $areaArray
	 * @return Area
	 */
	public static function create(array $areaArray): Area
	{
		return new Area(
			new Point($areaArray[6], $areaArray[7]),
			new Point($areaArray[0], $areaArray[1]),
			new Point($areaArray[2], $areaArray[3]),
			new Point($areaArray[4], $areaArray[5]),
		);
	}

	/**
	 * 左辺のX軸を取得。
	 *
	 * @return int
	 */
	public function left(): int
	{
		return min($this->leftTop->x, $this->leftBottom->x);
	}
	/**
	 * 上辺のY軸を取得。
	 *
	 * @return int
	 */
	public function top(): int
	{
		return min($this->leftTop->y, $this->rightTop->y);
	}
	/**
	 * 右辺のX軸を取得。
	 *
	 * @return int
	 */
	public function right(): int
	{
		return max($this->rightTop->x, $this->rightBottom->x);
	}
	/**
	 * 下辺のY軸を取得。
	 *
	 * @return int
	 */
	public function bottom(): int
	{
		return max($this->leftBottom->y, $this->rightBottom->y);
	}

	/**
	 * 幅を取得。
	 *
	 * @return int
	 */
	public function width(): int
	{
		return $this->rightTop->x - $this->leftBottom->x;
	}
	/**
	 * 高さを取得。
	 *
	 * @return int
	 */
	public function height(): int
	{
		return $this->leftBottom->y - $this->rightTop->y;
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return Code::toString(
			$this,
			Text::join(
				', ',
				[
					'leftTop: ' . $this->leftTop,
					'leftBottom: ' . $this->leftBottom,
					'rightBottom: ' . $this->rightBottom,
					'rightTop: ' . $this->rightTop,
				]
			)
		);
	}

	#endregion
}
