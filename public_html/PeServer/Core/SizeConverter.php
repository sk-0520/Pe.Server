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
	public array $terms = [
		'byte', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB',
	];

	/**
	 * Undocumented function
	 *
	 * @param integer $byteSize
	 * @param string $sizeFormat {f_size} {i_size} {term}
	 * @param string[]|null $terms
	 * @return string
	 */
	public function convertHumanReadableByte(int $byteSize, string $sizeFormat = '{i_size} {term}', ?array $terms = null): string
	{
		$size = $byteSize;
		$terms = $terms ?? $this->terms;
		$order = 0;
		while ($size >= self::KB_SIZE && ++$order < ArrayUtility::getCount($terms)) {
			$size = $size / self::KB_SIZE;
		}

		return StringUtility::replaceMap($sizeFormat, [
			'f_size' =>  strval(round($size, 2)),
			'i_size' => (int)strval($size),
			'term' => $terms[$order]
		]);
	}
}
