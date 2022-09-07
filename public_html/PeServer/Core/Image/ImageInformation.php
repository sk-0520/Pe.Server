<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use PeServer\Core\Image\ImageType;
use PeServer\Core\Image\Size;
use PeServer\Core\Throws\ImageException;

/**
 * 画像情報。
 *
 * @immutable
 */
class ImageInformation
{
	/**
	 * 生成
	 *
	 * @param Size $size
	 * @param string $mime
	 * @param int $type
	 * @phpstan-param ImageType::* $type
	 */
	private function __construct(
		public Size $size,
		public string $mime,
		public int $type
	) {
	}

	/**
	 * ファイルからイメージサイズを取得。
	 *
	 * `getimagesize` ラッパー。
	 *
	 * @param string $filePath 対象画像ファイルパス。
	 * @return ImageInformation
	 * @throws ImageException
	 * @see https://www.php.net/manual/function.getimagesize.php
	 */
	public static function load(string $filePath): ImageInformation
	{
		$result = getimagesize($filePath);
		if ($result === false) {
			throw new ImageException($filePath);
		}

		return new ImageInformation(
			new Size(
				$result[0],
				$result[1]
			),
			$result['mime'],
			$result[2]
		);
	}
}
