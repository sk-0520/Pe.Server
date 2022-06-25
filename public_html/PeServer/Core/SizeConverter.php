<?php

declare(strict_types=1);

namespace PeServer\Core;

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
	public array $units = [
		'byte', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB',
	];

	/**
	 * 読みやすいように変換。
	 *
	 * C#(Pe.Core)から移植。
	 *
	 * @param integer $byteSize
	 * @param string $sizeFormat {f_size} {i_size} {unit}
	 * @param string[]|null $units
	 * @return string
	 */
	public function convertHumanReadableByte(int $byteSize, string $sizeFormat = '{i_size} {unit}', ?array $units = null): string
	{
		$size = $byteSize;
		$units = $units ?? $this->units;
		$order = 0;
		while ($size >= self::KB_SIZE && ++$order < ArrayUtility::getCount($units)) {
			$size = $size / self::KB_SIZE;
		}

		return StringUtility::replaceMap($sizeFormat, [
			'f_size' => strval(round($size, 2)),
			'i_size' => strval($size),
			'unit' => $units[$order]
		]);
	}
}
