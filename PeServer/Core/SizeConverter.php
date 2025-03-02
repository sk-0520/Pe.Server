<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Throws\ArgumentException;

/**
 * データ容量変換。
 */
readonly class SizeConverter
{
	#region define

	/**
	 * 1KB のサイズ。
	 */
	public const KB_SIZE = 1024;

	/**
	 * サイズ単位。
	 *
	 * @var string[]
	 */
	public const UNITS = [
		'byte',
		'KB',
		'MB',
		'GB',
		'TB',
		'PB',
		'EB',
		'ZB',
		'YB',
	];

	#endregion

	/**
	 * 生成。
	 *
	 * @param int $kbSize 1KB のサイズ。
	 * @param string[] $units サイズ単位。
	 */
	public function __construct(
		private int $kbSize = self::KB_SIZE,
		private array $units = self::UNITS
	) {
	}

	#region function

	/**
	 * 読みやすいように変換。
	 *
	 * C#(Pe.Core)から移植。
	 *
	 * @param int $byteSize
	 * @param string $sizeFormat {f_size} {i_size} {unit}
	 * @phpstan-param literal-string $sizeFormat
	 * @return string
	 */
	public function convertHumanReadableByte(int $byteSize, string $sizeFormat = '{i_size} {unit}'): string
	{
		$size = $byteSize;
		$order = 0;
		$unitCount = Arr::getCount($this->units);
		while ($size >= $this->kbSize && ++$order < $unitCount) {
			$size = $size / $this->kbSize;
		}

		return Text::replaceMap($sizeFormat, [
			'f_size' => strval(round($size, 2)),
			'i_size' => strval($size),
			'unit' => $this->units[$order]
		]);
	}

	/**
	 * 基底の倍数に切り上げ。
	 *
	 * @param int $input 入力値。
	 * @param positive-int $base 基底の倍数。
	 * @return positive-int
	 */
	public function ceiling(int $input, int $base): int
	{
		// @phpstan-ignore smaller.alwaysFalse
		if ($base < 1) {
			throw new ArgumentException(Code::nameof($base));
		}
		if ($input < 1) {
			return $base;
		}

		$raw = ceil($input / $base) * $base;
		$result = (int)$raw;
		assert(0 < $result);
		return $result;
	}

	#endregion
}
