<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use Stringable;
use PeServer\Core\Code;
use PeServer\Core\Throws\ArgumentException;

/**
 * 幅と高さを持つ。
 *
 * @immutable
 */
class Size implements Stringable
{
	/**
	 * 生成
	 *
	 * @param int $width 横幅。
	 * @phpstan-param positive-int $width
	 * @param int $height 高さ。
	 * @phpstan-param positive-int $height
	 */
	public function __construct(
		public int $width,
		public int $height
	) {
		if ($width < 1) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$width');
		}
		if ($height < 1) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$height');
		}
	}

	#region Stringable

	public function __toString(): string
	{
		return Code::toString($this, ['width', 'height']);
	}

	#endregion
}
