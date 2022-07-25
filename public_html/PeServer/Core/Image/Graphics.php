<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \GdImage;
use PeServer\Core\DisposerBase;
use PeServer\Core\Image\ImageInformation;
use PeServer\Core\Throws\GraphicsException;
use PeServer\Core\TypeConverter;

/**
 * GD関数ラッパー。
 *
 * @see https://www.php.net/manual/book.image.php
 */
class Graphics extends DisposerBase
{
	private GdImage $image;

	private function __construct(GdImage $image)
	{
		$this->image = $image;
	}

	/**
	 * @see https://www.php.net/manual/function.imagedestroy.php
	 */
	protected function disposeImpl(): void
	{
		parent::disposeImpl();

		imagedestroy($this->image);
	}

	/**
	 * `gd_info` ラッパー。
	 *
	 * @return array<string,string|bool>
	 * @see https://www.php.net/manual/function.gd-info.php
	 */
	public static function getInformation(): array
	{
		return gd_info();
	}

	/**
	 * 画像の大きさを指定して生成。
	 *
	 * @param Size $size
	 * @return Graphics
	 * @throws GraphicsException
	 */
	public static function create(Size $size): Graphics
	{
		$image = imagecreatetruecolor($size->width, $size->height);
		if ($image === false) {
			throw new GraphicsException();
		}

		return new Graphics($image);
	}

	/**
	 * ファイルからイメージサイズを取得。
	 *
	 * `getimagesize` ラッパー。
	 *
	 * @param string $filePath 対象画像ファイルパス。
	 * @return ImageInformation
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.getimagesize.php
	 */
	public static function getImageInformation(string $filePath): ImageInformation
	{
		$result = getimagesize($filePath);
		if ($result === false) {
			throw new GraphicsException($filePath);
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

	/**
	 * DPI取得。
	 *
	 * @return Size
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imageresolution.php
	 */
	public function getDpi(): Size
	{
		$result = imageresolution($this->image);
		if ($result === false) {
			throw new GraphicsException('imageresolution');
		}
		assert(is_array($result));

		return new Size($result[0], $result[1]);
	}
	/**
	 * DPI設定。
	 *
	 * @param Size $size
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imageresolution.php
	 */
	public function setDpi(Size $size): void
	{
		$result = imageresolution($this->image, $size->width, $size->height);
		if ($result === false) {
			throw new GraphicsException('imageresolution: ' . $size);
		}
	}

	/**
	 * `imagecolorallocate` ラッパー。
	 *
	 * @param Color $color
	 * @return int
	 */
	public function attachColor(Color $color): int
	{
		$result = imagecolorallocate($this->image, $color->red, $color->green, $color->blue);
		if ($result === false) {
			throw new GraphicsException(TypeConverter::toString($color));
		}
		return $result;
	}
	/**
	 * `imagecolordeallocate` ラッパー。
	 *
	 * @param int $colorResource
	 * @return bool
	 */
	public function detachColor(int $colorResource): bool
	{
		return imagecolordeallocate($this->image, $colorResource);
	}
}
