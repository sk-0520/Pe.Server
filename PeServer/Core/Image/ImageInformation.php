<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use Exception;
use PeServer\Core\Errors\ErrorHandler;
use PeServer\Core\Image\ImageType;
use PeServer\Core\Image\Size;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Throws\ImageException;

/**
 * 画像情報。
 */
readonly class ImageInformation
{
	/**
	 * 生成
	 *
	 * @param Size $size
	 * @param string $mime
	 * @param ImageType $type
	 */
	private function __construct(
		public Size $size,
		public string $mime,
		public ImageType $type
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
		$result = ErrorHandler::trap(fn () => getimagesize($filePath));
		if ($result->isFailureOrFalse()) {
			throw new ImageException($filePath);
		}

		$result = $result->value;

		Throws::throwIf(1 <= $result[0]);
		Throws::throwIf(1 <= $result[1]);
		Throws::throwIf(-1 <= $result[2] && $result[2] <= 18 && $result[2] !== 0);

		return new ImageInformation(
			new Size(
				$result[0],
				$result[1]
			),
			$result['mime'],
			ImageType::from($result[2])
		);
	}
}
