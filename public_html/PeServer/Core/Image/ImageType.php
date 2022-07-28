<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

/**
 * `IMAGETYPE_*` ラッパー。
 */
abstract class ImageType
{
	/** こいつはラッパーじゃない。 */
	public const AUTO = -1;
	public const GIF = IMAGETYPE_GIF;
	public const JPEG = IMAGETYPE_JPEG;
	public const JPEG2000 = IMAGETYPE_JPEG2000;
	public const PNG = IMAGETYPE_PNG;
	public const SWF = IMAGETYPE_SWF;
	public const PSD = IMAGETYPE_PSD;
	public const BMP = IMAGETYPE_BMP;
	public const WBMP = IMAGETYPE_WBMP;
	public const XBM = IMAGETYPE_XBM;
	public const TIFF_II = IMAGETYPE_TIFF_II;
	public const TIFF_MM = IMAGETYPE_TIFF_MM;
	public const IFF = IMAGETYPE_IFF;
	public const JB2 = IMAGETYPE_JB2;
	public const JPC = IMAGETYPE_JPC;
	public const JP2 = IMAGETYPE_JP2;
	public const JPX = IMAGETYPE_JPX;
	public const SWC = IMAGETYPE_SWC;
	public const ICO = IMAGETYPE_ICO;
	public const WEBP = IMAGETYPE_WEBP;

	/**
	 * MIME取得。
	 *
	 * `image_type_to_mime_type` ラッパー。
	 *
	 * @param int $imageType
	 * @phpstan-param ImageType::* $imageType
	 * @return string
	 * @see https://www.php.net/manual/function.image-type-to-mime-type.php
	 */
	public static function toMime(int $imageType): string
	{
		return image_type_to_mime_type($imageType);
	}
}
