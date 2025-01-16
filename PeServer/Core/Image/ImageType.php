<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use PeServer\Core\Throws\ImageException;

/**
 * `IMAGETYPE_*` ラッパー。
 */
enum ImageType: int
{
	#region define

	/** こいつはラッパーじゃない。 */
	case Auto = -1;
	case Gif  = IMAGETYPE_GIF;
	case Jpeg = IMAGETYPE_JPEG;
	case Jpeg2K = IMAGETYPE_JPEG2000;
	case Png = IMAGETYPE_PNG;
	case Swf = IMAGETYPE_SWF;
	case Psd = IMAGETYPE_PSD;
	case Bmp = IMAGETYPE_BMP;
	case Wbmp = IMAGETYPE_WBMP;
	case Xbm = IMAGETYPE_XBM;
	case TiffIi = IMAGETYPE_TIFF_II;
	case TiffMm = IMAGETYPE_TIFF_MM;
	case Iff = IMAGETYPE_IFF;
	case Jb2 = IMAGETYPE_JB2;
	case Jp2 = IMAGETYPE_JP2;
	case Jpx = IMAGETYPE_JPX;
	case Swc = IMAGETYPE_SWC;
	case Ico = IMAGETYPE_ICO;
	case Webp = IMAGETYPE_WEBP;

	#endregion

	#region function

	/**
	 * MIME取得。
	 *
	 * `image_type_to_mime_type` ラッパー。
	 *
	 * @return string
	 * @see https://www.php.net/manual/function.image-type-to-mime-type.php
	 * @phpstan-pure
	 */
	public function toMime(): string
	{
		return image_type_to_mime_type($this->value);
	}

	/**
	 * 拡張子を取得。
	 *
	 * `image_type_to_extension` ラッパー。
	 *
	 * @param bool $dot 拡張子の前にドットをつけるかどうか。
	 * @return string
	 * @throws ImageException
	 * @see https://www.php.net/manual/function.image-type-to-extension.php
	 */
	public function toExtension(bool $dot = false): string
	{
		$result = image_type_to_extension($this->value, $dot);
		if ($result === false) {
			throw new ImageException();
		}

		return $result;
	}

	#endregion
}
