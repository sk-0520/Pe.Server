<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * データ容量変換。
 */
class SizeConverter
{
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
		'byte', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB',
	];

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

	/**
	 * 読みやすいように変換。
	 *
	 * C#(Pe.Core)から移植。
	 *
	 * @param integer $byteSize
	 * @param string $sizeFormat {f_size} {i_size} {unit}
	 * @phpstan-param literal-string $sizeFormat
	 * @return string
	 */
	public function convertHumanReadableByte(int $byteSize, string $sizeFormat = '{i_size} {unit}'): string
	{
		$size = $byteSize;
		$order = 0;
		$unitCount = ArrayUtility::getCount($this->units);
		while ($size >= $this->kbSize && ++$order < $unitCount) {
			$size = $size / $this->kbSize;
		}

		return StringUtility::replaceMap($sizeFormat, [
			'f_size' => strval(round($size, 2)),
			'i_size' => strval($size),
			'unit' => $this->units[$order]
		]);
	}
}
