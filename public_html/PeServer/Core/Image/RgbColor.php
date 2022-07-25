<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \Stringable;
use PeServer\Core\Code;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\ArgumentException;

/**
 * @immutable UnsignedIntegerAlias
 */
class RgbColor implements IColor
{
	/**
	 * 生成。
	 *
	 * @param int $red
	 * @phpstan-param int<0,255> $red
	 * @param int $green
	 * @phpstan-param int<0,255> $green
	 * @param int $blue
	 * @phpstan-param int<0,255> $blue
	 */
	public function __construct(
		public int $red,
		public int $green,
		public int $blue
	) {
		if ($red < 0 || 255 < $red) { //@phpstan-ignore-line
			throw new ArgumentException('$red');
		}
		if ($green < 0 || 255 < $green) { //@phpstan-ignore-line
			throw new ArgumentException('$green');
		}
		if ($blue < 0 || 255 < $blue) { //@phpstan-ignore-line
			throw new ArgumentException('$blue');
		}
	}

	public static function fromHtmlColorCode(string $htmlColor): RgbColor
	{
		$offset = 0;
		return new RgbColor(
			/** @phpstan-var int<0, 255> */
			(int)hexdec(substr($htmlColor, 1 - $offset, 2)), //@phpstan-ignore-line
			/** @phpstan-var int<0, 255> */
			(int)hexdec(substr($htmlColor, 3 - $offset, 2)), //@phpstan-ignore-line
			/** @phpstan-var int<0, 255> */
			(int)hexdec(substr($htmlColor, 5 - $offset, 2)) //@phpstan-ignore-line
		);
	}

	public function __toString(): string
	{
		return Code::toString($this, StringUtility::format('#%02x%02x%02x', $this->red, $this->green, $this->blue));
	}
}
