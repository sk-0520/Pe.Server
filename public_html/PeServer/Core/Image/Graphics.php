<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use \GdImage;
use PeServer\Core\Alignment;
use PeServer\Core\Binary;
use PeServer\Core\DisposerBase;
use PeServer\Core\ErrorHandler;
use PeServer\Core\FileUtility;
use PeServer\Core\IDisposable;
use PeServer\Core\Image\Area;
use PeServer\Core\Image\Color\ColorResource;
use PeServer\Core\Image\Color\IColor;
use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\GraphicsException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeConverter;

/**
 * GD関数ラッパー。
 *
 * @see https://www.php.net/manual/book.image.php
 */
class Graphics extends DisposerBase
{
	public const CURRENT_THICKNESS = -1;
	public const DEFAULT_THICKNESS = 1;

	public GdImage $image;
	/** @phpstan-var positive-int */
	private int $thickness;
	/** @phpstan-ignore-next-line */
	private bool $antiAlias;

	private function __construct(GdImage $image, bool $isEnabledAlpha)
	{
		$this->image = $image;

		$this->thickness = self::DEFAULT_THICKNESS;
		imagesetthickness($this->image, $this->thickness);

		if ($isEnabledAlpha) {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
		}

		$this->antiAlias = imageantialias($this->image, true);
	}

	/**
	 * @see https://www.php.net/manual/function.imagedestroy.php
	 */
	protected function disposeImpl(): void
	{
		imagedestroy($this->image);

		parent::disposeImpl();
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

		return new Graphics($image, true);
	}

	/**
	 * バイナリデータから生成。
	 *
	 * @param Binary $binary
	 * @param int $imageType
	 * @phpstan-param ImageType::* $imageType
	 * @return Graphics
	 */
	public static function load(Binary $binary, int $imageType = ImageType::AUTO): Graphics
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

		return new Graphics($result->value, true);
	}

	/**
	 * ファイルから生成。
	 *
	 * @param string $path
	 * @phpstan-param non-empty-string $path
	 * @return Graphics
	 */
	public static function open(string $path): Graphics
	{
		$binary = FileUtility::readContent($path);
		return self::load($binary);
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
	 * @param int $scaleMode 変換フラグ。
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

		return new Graphics($result, true);
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
			$rgb & 0xff, //@phpstan-ignore-line 0xff,
			($rgb >> 24) & 0x7f //@phpstan-ignore-line 0xff,
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
	 * 線幅設定。
	 *
	 * @param int $thickness
	 * @phpstan-param positive-int $thickness
	 */
	public function setThickness(int $thickness): void
	{
		$result = imagesetthickness($this->image, $thickness);
		if ($result === false) {
			throw new GraphicsException();
		}

		$this->thickness = $thickness;
	}

	/**
	 * 線幅適用。
	 *
	 * @param int $thickness
	 * @phpstan-param positive-int $thickness
	 * @return IDisposable 戻し。
	 */
	private function applyThickness(int $thickness): IDisposable
	{
		if ($thickness === $this->thickness) {
			return DisposerBase::empty();
		}
		if ($thickness < 1) { //@phpstan-ignore-line
			throw new ArgumentException('$thickness');
		}

		$restoreThickness = $this->thickness;
		$this->setThickness($thickness);

		return new class($this, $restoreThickness) extends DisposerBase
		{
			/**
			 * 生成。
			 *
			 * @param Graphics $graphics
			 * @param int $restoreThickness
			 * @phpstan-param positive-int $restoreThickness
			 */
			public function __construct(
				private Graphics $graphics,
				private int $restoreThickness
			) {
			}

			protected function disposeImpl(): void
			{
				$this->graphics->setThickness($this->restoreThickness);

				parent::disposeImpl();
			}
		};
	}

	/**
	 * `imagecolorallocate` ラッパー。
	 *
	 * @param RgbColor $color
	 * @return ColorResource
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imagecolorallocatealpha.php
	 * @see https://www.php.net/manual/function.imagecolorallocate.php
	 */
	public function attachColor(RgbColor $color): ColorResource
	{
		$result = $color->alpha !== RgbColor::ALPHA_NONE
			? imagecolorallocatealpha($this->image, $color->red, $color->green, $color->blue, $color->alpha)
			: imagecolorallocate($this->image, $color->red, $color->green, $color->blue);
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

	/**
	 * 矩形塗り潰し。
	 *
	 * @param IColor $color 色。
	 * @param Rectangle $rectangle 矩形領域。
	 */
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
	 * 矩形描画。
	 *
	 * @param IColor $color
	 * @param Rectangle $rectangle
	 * @param int $thickness
	 * @phpstan-param positive-int|self::CURRENT_THICKNESS $thickness
	 */
	public function drawRectangle(IColor $color, Rectangle $rectangle, int $thickness = self::CURRENT_THICKNESS): void
	{
		$restore = $thickness === self::CURRENT_THICKNESS
			? DisposerBase::empty()
			: $this->applyThickness($thickness);

		try {
			$result = $this->doColor(
				$color,
				fn ($attachedColor) => imagerectangle(
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
		} finally {
			$restore->dispose();
		}
	}

	/**
	 * テキスト描画領域取得。
	 *
	 * @param string $text
	 * @param float $fontSize
	 * @param string $fontNameOrPath
	 * @param float $angle
	 * @return Area
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imageftbbox.php
	 */
	public static function calculateTextArea(string $text, float $fontSize, string $fontNameOrPath, float $angle): Area
	{
		$options = [];
		/** @phpstan-var non-empty-array<UnsignedIntegerAlias>|false */
		$result = imageftbbox($fontSize, $angle, $fontNameOrPath, $text, $options);
		if ($result === false) {
			throw new GraphicsException();
		}

		return Area::create($result);
	}

	/**
	 * テキスト描画。
	 *
	 * @param string $text 描画テキスト。
	 * @param float $fontSize フォントサイズ。
	 * @param Point $location 描画開始座標。
	 * @param IColor $color 描画色。
	 * @param TextSetting $setting 描画するテキスト設定。
	 * @return Area 描画領域。
	 * @throws GraphicsException
	 */
	public function drawString(string $text, float $fontSize, Point $location, IColor $color, TextSetting $setting): Area
	{
		/** @phpstan-var non-empty-array<UnsignedIntegerAlias>|false */
		$result = $this->doColor(
			$color,
			fn ($attachedColor) => imagettftext(
				$this->image,
				$fontSize,
				$setting->angle,
				(int)$location->x,
				(int)$location->y,
				$attachedColor,
				$setting->fontNameOrPath,
				$text
			)
		);

		if ($result === false) {
			throw new GraphicsException();
		}

		return Area::create($result);
	}

	/**
	 * テキスト描画。
	 *
	 * 内部的に `self::calculateTextArea` を使用。
	 *
	 * @param string $text 描画テキスト。
	 * @param float $fontSize フォントサイズ。
	 * @param Rectangle $rectangle 描画する矩形。
	 * @param IColor $color 描画色。
	 * @param TextSetting $setting 描画するテキスト設定。
	 * @return Area 描画領域。
	 * @throws GraphicsException
	 * @see https://php.net/manual/en/function.imagettftext.php
	 */
	public function drawText(string $text, float $fontSize, Rectangle $rectangle, IColor $color, TextSetting $setting): Area
	{
		$fontArea = self::calculateTextArea($text, $fontSize, $setting->fontNameOrPath, $setting->angle);

		$x = match ($setting->horizontal) {
			Alignment::HORIZONTAL_LEFT => $rectangle->left() - min($fontArea->left(), $fontArea->right()),
			Alignment::HORIZONTAL_CENTER  => $rectangle->left() + ($rectangle->size->width / 2) - ($fontArea->width() / 2),
			Alignment::HORIZONTAL_RIGHT => $rectangle->right() - max($fontArea->left(), $fontArea->right()), //@phpstan-ignore-line
			default => throw new ArgumentException('$horizontal: ' . $setting->horizontal), //@phpstan-ignore-line
		};
		$y = match ($setting->vertical) {
			Alignment::VERTICAL_TOP => $rectangle->top() - min($fontArea->top(), $fontArea->bottom()),
			Alignment::VERTICAL_CENTER => $rectangle->top() + ($rectangle->size->height / 2) + ($fontArea->height() / 2),
			Alignment::VERTICAL_BOTTOM => $rectangle->bottom() - max($fontArea->top(), $fontArea->bottom()), //@phpstan-ignore-line
			default => throw new ArgumentException('$vertical: ' . $setting->vertical), //@phpstan-ignore-line
		};

		return $this->drawString(
			$text,
			$fontSize,
			new Point((int)$x, (int)$y),
			$color,
			$setting
		);
	}

	private function exportImageCore(ImageOption $option): Binary
	{
		return OutputBuffer::get(fn () => match ($option->imageType) {
			ImageType::PNG => imagepng($this->image, null, ...$option->options()),
			ImageType::JPEG => imagejpeg($this->image, null, ...$option->options()),
			ImageType::WEBP => imagewebp($this->image, null, ...$option->options()),
			ImageType::BMP => imagebmp($this->image, null, ...$option->options()),
			default  => throw new NotImplementedException(),
		});
	}

	/**
	 * 画像データ出力。
	 *
	 * @param ImageOption $option
	 * @return Binary
	 */
	public function exportImage(ImageOption $option): Binary
	{
		if ($option->imageType == ImageType::AUTO) {
			throw new ArgumentException('ImageType::AUTO');
		}

		return $this->exportImageCore($option);
	}

	public function exportHtmlSource(ImageOption $option): string
	{
		if ($option->imageType == ImageType::AUTO) {
			throw new ArgumentException('ImageType::AUTO');
		}

		$image = $this->exportImageCore($option);

		$mime = match ($option->imageType) {
			default => ImageType::toMime($option->imageType),
		};

		$data = 'data:' . $mime . ';base64,';
		$body = $image->toBase64();

		$result = $data . $body;

		return $result;
	}
}
