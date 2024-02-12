<?php

declare(strict_types=1);

namespace PeServer\Core\Image\Color;

use Stringable;

/**
 * GD関数ラッパー処理での色。
 */
interface IColor extends Stringable
{
	/** 不透明。 */
	public const ALPHA_NONE = 0;
	/** 完全な透明。 */
	public const ALPHA_FULL = 127;

	/**
	 * HTML的なカラーコードに変換。
	 *
	 * CSS も若干あれなこう、やることが多いあれ。
	 *
	 * @return string
	 */
	public function toHtml(): string;
}
