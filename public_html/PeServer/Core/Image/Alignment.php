<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

/**
 * 配置。
 */
abstract class Alignment
{
	#region define

	/** 水平方向: 左。 */
	const HORIZONTAL_LEFT = 10;
	/** 水平方向: 中央。 */
	const HORIZONTAL_CENTER = 11;
	/** 水平方向: 右。 */
	const HORIZONTAL_RIGHT = 12;

	/** 垂直方向: 上。 */
	const VERTICAL_TOP = 20;
	/** 垂直方向: 中央。 */
	const VERTICAL_CENTER = 21;
	/** 垂直方向: 下。 */
	const VERTICAL_BOTTOM = 22;

	#endregion
}
