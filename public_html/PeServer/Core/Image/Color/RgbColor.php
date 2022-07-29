<?php

declare(strict_types=1);

namespace PeServer\Core\Image\Color;

use \Serializable;
use \Stringable;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Code;
use PeServer\Core\Regex;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\NotSupportedException;

/**
 * @immutable
 */
class RgbColor implements IColor, Serializable
{
	public const RGB_MINIMUM = 0;
	public const RGB_MAXIMUM = 255;

	/**
	 * 生成。
	 *
	 * @param int $red
	 * @phpstan-param int<self::RGB_MINIMUM,self::RGB_MAXIMUM> $red
	 * @param int $green
	 * @phpstan-param int<self::RGB_MINIMUM,self::RGB_MAXIMUM> $green
	 * @param int $blue
	 * @phpstan-param int<self::RGB_MINIMUM,self::RGB_MAXIMUM> $blue
	 * @param int $alpha
	 * @phpstan-param int<IColor::ALPHA_NONE,IColor::ALPHA_FULL> $alpha
	 */
	public function __construct(
		public int $red,
		public int $green,
		public int $blue,
		public int $alpha = IColor::ALPHA_NONE
	) {
		if ($red < self::RGB_MINIMUM || self::RGB_MAXIMUM < $red) { //@phpstan-ignore-line
			throw new ArgumentException('$red');
		}
		if ($green < self::RGB_MINIMUM || self::RGB_MAXIMUM < $green) { //@phpstan-ignore-line
			throw new ArgumentException('$green');
		}
		if ($blue < self::RGB_MINIMUM || self::RGB_MAXIMUM < $blue) { //@phpstan-ignore-line
			throw new ArgumentException('$blue');
		}
		if ($alpha < self::ALPHA_NONE || self::ALPHA_FULL < $alpha) { //@phpstan-ignore-line
			throw new ArgumentException('$alpha');
		}
	}

	/**
	 * TODO: 透明度できてない。
	 *
	 * @param string $s
	 * @param bool $isAlpha
	 * @return int
	 * @phpstan-return ($isAlpha is true ? int<IColor::ALPHA_NONE,IColor::ALPHA_FULL>: int<self::RGB_MINIMUM,self::RGB_MAXIMUM>)
	 */
	private static function fromHex(string $s, bool $isAlpha/* 0-127に制限が必要 */): int
	{
		if (StringUtility::getByteCount($s) === 1) {
			$s .= $s;
		}

		$result = (int)hexdec($s);

		if ($isAlpha) {
			/** @phpstan-var int<IColor::ALPHA_NONE,IColor::ALPHA_FULL> */
			return (int)($result / 2);
		};

		/** @phpstan-var int<self::RGB_MINIMUM,self::RGB_MAXIMUM> */
		return $result;
	}

	public static function fromHtmlColorCode(string $htmlColor): RgbColor
	{
		if (StringUtility::isNullOrWhiteSpace($htmlColor)) {
			throw new ArgumentException($htmlColor);
		}
		$strColor = StringUtility::trim($htmlColor);
		if (StringUtility::isNullOrEmpty($strColor)) {
			throw new ArgumentException($htmlColor);
		}

		$matchers = Regex::matches($htmlColor, '/#?(?<R>[0-9A-fa-f]{1,2})(?<G>[0-9A-fa-f]{1,2})(?<B>[0-9A-fa-f]{1,2})(?<A>[0-9A-fa-f]{1,2})?/');

		if (ArrayUtility::isNullOrEmpty($matchers)) {
			//rgb()的な奴は知らん
			throw new NotSupportedException();
		}

		$r = self::fromHex($matchers['R'], false);
		$g = self::fromHex($matchers['G'], false);
		$b = self::fromHex($matchers['B'], false);

		if (StringUtility::isNullOrEmpty($matchers['A'])) {
			return new RgbColor($r, $g, $b);
		}

		$a = self::fromHex($matchers['A'], true);
		return new RgbColor($r, $g, $b, $a);
	}

	public function toHtml(): string
	{
		if ($this->alpha === IColor::ALPHA_NONE) {
			return StringUtility::format('#%02x%02x%02x', $this->red, $this->green, $this->blue);
		}

		return StringUtility::format('#%02x%02x%02x%02x', $this->red, $this->green, $this->blue, $this->alpha);
	}

	public function serialize(): string
	{
		$values = [
			'r' => $this->red,
			'g' => $this->green,
			'b' => $this->blue,
		];
		if ($this->alpha !== IColor::ALPHA_NONE) {
			$values['a'] = $this->alpha;
		}

		return serialize($values);
	}

	public function unserialize(string $data): void
	{
		$values = unserialize($data);
		$this->red = (int)$values['r']; //@phpstan-ignore-line Serializable
		$this->green = (int)$values['g']; //@phpstan-ignore-line Serializable
		$this->blue = (int)$values['b']; //@phpstan-ignore-line Serializable
		if (isset($values['a'])) {
			$this->alpha = (int)$values['a']; //@phpstan-ignore-line Serializable
		} else {
			$this->alpha = self::ALPHA_NONE; //@phpstan-ignore-line Serializable
		}
	}

	public function __toString(): string
	{
		return Code::toString($this, StringUtility::format('#%02x%02x%02x-%02x', $this->red, $this->green, $this->blue, $this->alpha));
	}
}
