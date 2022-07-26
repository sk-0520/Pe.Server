<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \GdImage;
use PeServer\Core\Binary;
use PeServer\Core\DisposerBase;
use PeServer\Core\ErrorHandler;
use PeServer\Core\IDisposable;
use PeServer\Core\Image\Area;
use PeServer\Core\Image\ColorResource;
use PeServer\Core\Image\IColor;
use PeServer\Core\Image\ImageInformation;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Throws\CoreError;
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

	public static function load(Binary $binary, int $imageType = -1): Graphics
	{
		$funcName = match ($imageType) {
			ImageType::PNG => 'imagecreatefrompng',
			ImageType::JPEG => 'imagecreatefromjpeg',
			ImageType::WEBP => 'imagecreatefromwebp',
			ImageType::BMP => 'imagecreatefrombmp',
			default => 'imagecreatefromstring'
		};

		$result = ErrorHandler::trapError(
			fn () => call_user_func($funcName, $binary->getRaw())
		);

		if (!$result->success) {
			throw new GraphicsException();
		}

		return new Graphics($result->value);
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
	 * 画像サイズを取得
	 *
	 * @return Size
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imagesx.php
	 * @see https://www.php.net/manual/function.imagesy.php
	 */
	public function getSize(): Size
	{
		$width = imagesx($this->image);
		if ($width === false) { //@phpstan-ignore-line
			throw new GraphicsException();
		}
		if ($width < 1) {
			throw new GraphicsException();
		}
		$height = imagesy($this->image);
		if ($height === false) { //@phpstan-ignore-line
			throw new GraphicsException();
		}
		if ($height < 1) {
			throw new GraphicsException();
		}

		return new Size($width, $height);
	}

	/**
	 * 縮尺を変更。
	 *
	 * @param int|Size $size int: 横幅のみ、高さは自動設定される。 Size:幅・高さ
	 * @phpstan positive-int|Size $size
	 * @param int $scaleMode
	 * @phpstan-param ScaleMode::* $scaleMode
	 * @return Graphics
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imagescale.php
	 */
	public function scale(int|Size $size, int $scaleMode): Graphics
	{
		$result = false;
		if (is_int($size)) {
			$result = imagescale($this->image, $size, -1, $scaleMode);
		} else {
			$result = imagescale($this->image, $size->width, $size->height, $scaleMode);
		}
		if ($result === false) {
			throw new GraphicsException();
		}

		return new Graphics($result);
	}

	/**
	 * 指定したピクセルの色を取得。
	 *
	 * `imagecolorat` ラッパー。
	 *
	 * @param Point $point
	 * @return RgbColor
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imagecolorat.php
	 */
	public function getPixel(Point $point): RgbColor
	{
		$rgb = imagecolorat($this->image, $point->x, $point->y);
		if ($rgb === false) {
			throw new GraphicsException();
		}

		return new RgbColor(
			($rgb >> 16) & 0xff, //@phpstan-ignore-line 0xff
			($rgb >> 8) & 0xff, //@phpstan-ignore-line 0xff
			$rgb & 0xff //@phpstan-ignore-line 0xff
		);
	}

	/**
	 * 指定したピクセルの色を設定。
	 *
	 * `imagesetpixel` ラッパー。
	 *
	 * @param Point $point
	 * @param IColor $color
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imagesetpixel.php
	 */
	public function setPixel(Point $point, IColor $color): void
	{
		$result = $this->doColor(
			$color,
			fn ($attachedColor) => imagesetpixel(
				$this->image,
				$point->x,
				$point->y,
				$attachedColor
			)
		);

		if (!$result) {
			throw new GraphicsException();
		}
	}

	/**
	 * `imagecolorallocate` ラッパー。
	 *
	 * @param RgbColor $color
	 * @return ColorResource
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imagecolorallocate.php
	 */
	public function attachColor(RgbColor $color): ColorResource
	{
		$result = imagecolorallocate($this->image, $color->red, $color->green, $color->blue);
		if ($result === false) {
			throw new GraphicsException(TypeConverter::toString($color));
		}
		return new ColorResource($this, $result);
	}
	/**
	 * `imagecolordeallocate` ラッパー。
	 *
	 * @param ColorResource $colorResource
	 * @return bool
	 */
	public function detachColor(ColorResource $colorResource): bool
	{
		return imagecolordeallocate($this->image, $colorResource->value);
	}

	private function doColorCore(ColorResource $color, callable $action): mixed
	{
		return $action($color->value);
	}

	private function doColor(IColor $color, callable $action): mixed
	{
		if ($color instanceof RgbColor) {
			$colorResource = $this->attachColor($color);
			try {
				return $this->doColorCore($colorResource, $action);
			} finally {
				$this->detachColor($colorResource);
			}
		} else {
			assert($color instanceof ColorResource);
			return $this->doColorCore($color, $action);
		}
	}

	public function fillRectangle(IColor $color, Rectangle $rectangle): void
	{
		$result = $this->doColor(
			$color,
			fn ($attachedColor) => imagefilledrectangle(
				$this->image,
				$rectangle->left(),
				$rectangle->top(),
				$rectangle->right(),
				$rectangle->bottom(),
				$attachedColor
			)
		);
		if ($result === false) {
			throw new GraphicsException();
		}
	}

	/**
	 * テキスト描画領域取得。
	 *
	 * @param string $text
	 * @param string $fontNameOrPath
	 * @param float $fontSize
	 * @param float $angle
	 * @return Area
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imageftbbox.php
	 */
	public static function calculateTextArea(string $text, string $fontNameOrPath, float $fontSize, float $angle): Area
	{
		$options = [];
		$result = imageftbbox($fontSize, $angle, $fontNameOrPath, $text, $options);
		if ($result === false) {
			throw new GraphicsException();
		}

		return new Area(
			new Point($result[6], $result[7]),
			new Point($result[0], $result[1]),
			new Point($result[2], $result[3]),
			new Point($result[4], $result[5]),
		);
	}

	public function drawText(string $text, string $fontNameOrPath, float $fontSize, float $angle, Point $location, IColor $color): Area
	{
		$result = $this->doColor(
			$color,
			fn ($attachedColor) => imagettftext(
				$this->image,
				$fontSize,
				$angle,
				$location->x,
				$location->y,
				$attachedColor,
				$fontNameOrPath,
				$text
			)
		);

		if ($result === false) {
			throw new GraphicsException();
		}

		return new Area(
			new Point($result[6], $result[7]),
			new Point($result[0], $result[1]),
			new Point($result[2], $result[3]),
			new Point($result[4], $result[5]),
		);
	}

	/**
	 * 画像データ出力。
	 *
	 * @param ImageOption $option
	 * @return Binary
	 */
	public function toImage(ImageOption $option): Binary
	{
		return OutputBuffer::get(fn () => match ($option->imageType) {
			ImageType::PNG => imagepng($this->image, null, ...$option->options()),
			ImageType::JPEG => imagejpeg($this->image, null, ...$option->options()),
			ImageType::WEBP => imagewebp($this->image, null, ...$option->options()),
			ImageType::BMP => imagebmp($this->image, null, ...$option->options()),
			default  => throw new CoreError(),
		});
	}
}
