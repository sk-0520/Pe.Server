<?php

declare(strict_types=1);

namespace PeServer\Core\Version;

use Exception;
use Stringable;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ParseException;
use PeServer\Core\Throws\RegexException;
use PeServer\Core\TypeUtility;

/**
 * .NET のバージョンクラスと同じ扱い。
 */

readonly class CliVersion implements Stringable
{
	#region define

	public const IGNORE_REVISION = -1;

	#endregion

	/**
	 * 生成
	 *
	 * @param int $major
	 * @phpstan-param non-negative-int $major
	 * @param int $minor
	 * @phpstan-param non-negative-int $minor
	 * @param int $build
	 * @phpstan-param non-negative-int $build
	 * @param int $revision
	 * @phpstan-param self::IGNORE_REVISION|non-negative-int $revision
	 */
	public function __construct(int $major, int $minor = 0, int $build = 0, int $revision = self::IGNORE_REVISION)
	{
		$this->major = $major;
		$this->minor = $minor;
		$this->build = $build;
		$this->revision = 0 <= $revision ? $revision : self::IGNORE_REVISION;
	}

	#region property

	/**
	 * [1] メジャー バージョン。
	 * @phpstan-var non-negative-int
	 */
	public int $major;
	/**
	 * [2] マイナー バージョン。
	 * @phpstan-var non-negative-int
	 */
	public int $minor;
	/**
	 * [3] ビルド バージョン。
	 * @phpstan-var non-negative-int
	 */
	public int $build;
	/**
	 * [4] リビジョン バージョン。
	 * @phpstan-var -1|non-negative-int
	 */
	public int $revision;

	#endregion

	#region function

	/**
	 *
	 * @param null|string $s
	 * @param null|CliVersion $result
	 * @return bool
	 * @phpstan-assert-if-true CliVersion $result
	 * @phpstan-assert-if-false null $result
	 * @throws ArgumentException
	 * @throws RegexException
	 */
	private static function tryParseCore(?string $s, ?CliVersion &$result): bool
	{
		if (Text::isNullOrWhiteSpace($s)) {
			return false;
		}

		$regex = new Regex();
		try {
			$matches = $regex->matches($s, '/^(?<MAJOR>\d+)(\.(?<MINOR>\d+)(\.(?<BUILD>\d+)(\.(?<REVISION>\d+))?)?)?$/');
			$elementCount = Arr::getCount($matches);

			if ($elementCount === 0) {
				return false;
			}

			$major = 0;
			$minor = 0;
			$build = 0;
			$revision = self::IGNORE_REVISION;

			if (isset($matches['MAJOR'])) {
				$major = TypeUtility::parseUInteger($matches['MAJOR']);
			}
			if (isset($matches['MINOR'])) {
				if (TypeUtility::tryParseUInteger($matches['MINOR'], $value)) {
					$minor = $value;
				}
			}
			if (isset($matches['BUILD'])) {
				if (TypeUtility::tryParseUInteger($matches['BUILD'], $value)) {
					$build = $value;
				}
			}
			if (isset($matches['REVISION'])) {
				if (TypeUtility::tryParseUInteger($matches['REVISION'], $value)) {
					$revision = $value;
				}
			}

			$result = new self($major, $minor, $build, $revision);

			return true;
		} catch (Exception) {
			return false;
		}
	}

	public static function tryParse(?string $s, ?CliVersion &$result): bool
	{
		return self::tryParseCore($s, $result);
	}

	public static function parse(?string $s): CliVersion
	{
		if (self::tryParseCore($s, $result)) {
			return $result;
		}

		throw new ParseException();
	}

	public static function compare(CliVersion $a, CliVersion $b): int
    {
		if ($a->major != $b->major) {
			if ($a->major > $b->major) {
				return 1;
			}
			return -1;
		}

		if ($a->minor != $b->minor) {
			if ($a->minor > $b->minor) {
				return 1;
			}
			return -1;
		}

		if ($a->build != $b->build) {
			if ($a->build > $b->build) {
				return 1;
			}
			return -1;
		}

		if ($a->revision != $b->revision) {
			if ($a->revision > $b->revision) {
				return 1;
			}
			return -1;
		}

		return 0;
	}

	public function toCompare(CliVersion $version): int
	{
		return self::compare($this, $version);
	}

	public function isEquals(CliVersion $version): bool
	{
		return !self::compare($this, $version);
	}

	public function toString(): string
	{
		return $this->__toString();
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		$result = "{$this->major}.{$this->minor}.{$this->build}";
		if ($this->revision !== self::IGNORE_REVISION) {
			$result .= ".{$this->revision}";
		}

		return $result;
	}

	#endregion
}
