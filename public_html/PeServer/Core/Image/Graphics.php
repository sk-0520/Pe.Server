<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use GdImage;
use PeServer\Core\Binary;
use PeServer\Core\DisposerBase;
use PeServer\Core\ErrorHandler;
use PeServer\Core\IDisposable;
use PeServer\Core\Image\Alignment;
use PeServer\Core\Image\Area;
use PeServer\Core\Image\Color\ColorResource;
use PeServer\Core\Image\Color\IColor;
use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\Image\ImageSetting;
use PeServer\Core\Image\ImageType;
use PeServer\Core\Image\Point;
use PeServer\Core\Image\Rectangle;
use PeServer\Core\Image\ScaleMode;
use PeServer\Core\Image\Size;
use PeServer\Core\Image\TextSetting;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\GraphicsException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeUtility;
use PeServer\Core\Image\HorizontalAlignment;
use PeServer\Core\Image\VerticalAlignment;

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
	public static function create(Size $size): self
	{
		$image = imagecreatetruecolor($size->width, $size->height);
		if ($image === false) {
			throw new GraphicsException();
		}

		return new self($image, true);
	}

	/**
	 * バイナリデータから生成。
	 *
	 * @param Binary $binary
	 * @param ImageType $imageType
	 * @return Graphics
	 */
	public static function load(Binary $binary, ImageType $imageType = ImageType::Auto): self
	{
		$funcName = match ($imageType) {
			ImageType::Png => 'imagecreatefrompng',
			ImageType::Jpeg => 'imagecreatefromjpeg',
			ImageType::Webp => 'imagecreatefromwebp',
			ImageType::Bmp => 'imagecreatefrombmp', //cspell:disable-line
			default => 'imagecreatefromstring'
		};

		$result = ErrorHandler::trapError(
			fn () => call_user_func($funcName, $binary->raw)
		);

		if (!$result->success) {
			throw new GraphicsException();
		}

		return new self($result->value, true);
	}

	/**
	 * ファイルから生成。
	 *
	 * @param non-empty-string $path
	 * @return Graphics
	 */
	public static function open(string $path): self
	{
		$binary = File::readContent($path);
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
		$result = imageresolution($this->image);  //cspell:disable-line
		if ($result === false) {
			throw new GraphicsException('imageresolution'); //cspell:disable-line
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
		$result = imageresolution($this->image, $size->width, $size->height); //cspell:disable-line
		if ($result === false) {
			throw new GraphicsException('imageresolution: ' . $size); //cspell:disable-line
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
		if ($width === false) { //@phpstan-ignore-line [PHP_VERSION]
			throw new GraphicsException();
		}
		if ($width < 1) {
			throw new GraphicsException();
		}
		$height = imagesy($this->image);
		if ($height === false) { //@phpstan-ignore-line [PHP_VERSION]
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
	 * @param ScaleMode $scaleMode 変換フラグ。
	 * @return Graphics
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imagescale.php
	 */
	public function scale(int|Size $size, ScaleMode $scaleMode): self
	{
		$result = false;
		if (is_int($size)) {
			$result = imagescale($this->image, $size, -1, $scaleMode->value);
		} else {
			$result = imagescale($this->image, $size->width, $size->height, $scaleMode->value);
		}
		if ($result === false) {
			throw new GraphicsException();
		}

		return new self($result, true);
	}

	/**
	 * `imagerotate` ラッパー。
	 *
	 * @param float $angle
	 * @param IColor $backgroundColor
	 * @return Graphics
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imagerotate.php
	 */
	public function rotate(float $angle, IColor $backgroundColor): self
	{
		$result = $this->doColor(
			$backgroundColor,
			fn ($attachedColor) => imagerotate($this->image, $angle, $attachedColor)
		);
		if ($result === false) {
			throw new GraphicsException();
		}

		return new self($result, true);
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
			($rgb >> 16) & 0xff,
			($rgb >> 8) & 0xff,
			$rgb & 0xff,
			($rgb >> 24) & 0x7f
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
		if ($thickness < 1) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$thickness');
		}

		$restoreThickness = $this->thickness;
		$this->setThickness($thickness);

		//phpcs:ignore PSR12.Classes.AnonClassDeclaration.SpaceAfterKeyword
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
			throw new GraphicsException(TypeUtility::toString($color));
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

	/**
	 * 色処理の実行部分。
	 *
	 * @template TResult
	 * @param ColorResource $color
	 * @param callable $callback
	 * @phpstan-param callable(mixed): TResult $callback
	 * @return mixed
	 * @phpstan-return TResult
	 */
	private function doColorCore(ColorResource $color, callable $callback): mixed
	{
		return $callback($color->value);
	}

	/**
	 * 色の処理。
	 *
	 * @template TResult
	 * @param IColor $color
	 * @param callable $callback
	 * @phpstan-param callable(mixed): TResult $callback
	 * @return mixed
	 * @phpstan-return TResult
	 */
	private function doColor(IColor $color, callable $callback): mixed
	{
		if ($color instanceof RgbColor) {
			$colorResource = $this->attachColor($color);
			try {
				return $this->doColorCore($colorResource, $callback);
			} finally {
				$this->detachColor($colorResource);
			}
		} else {
			assert($color instanceof ColorResource);
			return $this->doColorCore($color, $callback);
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
	 * `imagettftext` ラッパー。
	 *
	 * @param string $text 描画テキスト。
	 * @param float $fontSize フォントサイズ。
	 * @param Point $location 描画開始座標。
	 * @param IColor $color 描画色。
	 * @param TextSetting $setting 描画するテキスト設定。
	 * @return Area 描画領域。
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/ja/function.imagettftext.php
	 */
	public function drawString(string $text, float $fontSize, Point $location, IColor $color, TextSetting $setting): Area
	{
		$result = $this->doColor(
			$color,
			fn ($attachedColor) => imagettftext(
				$this->image,
				$fontSize,
				$setting->angle,
				$location->x,
				$location->y,
				$attachedColor,
				$setting->fontNameOrPath,
				$text
			)
		);

		if ($result === false) {
			throw new GraphicsException();
		}

		//@phpstan-ignore-next-line ↑が false だけのはずなんだけど true を捕まえてる感じ
		return Area::create($result);
	}

	/**
	 * いい感じにテキスト描画。
	 *
	 * 内部的に `self::calculateTextArea`, `self::drawString` を使用。
	 *
	 * @param string $text 描画テキスト。
	 * @param float $fontSize フォントサイズ。
	 * @param Rectangle $rectangle 描画する矩形。
	 * @param IColor $color 描画色。
	 * @param TextSetting $setting 描画するテキスト設定。
	 * @return Area 描画領域。
	 * @throws GraphicsException
	 * @see https://www.php.net/manual/function.imagettftext.php
	 */
	public function drawText(string $text, float $fontSize, Rectangle $rectangle, IColor $color, TextSetting $setting): Area
	{
		$fontArea = self::calculateTextArea($text, $fontSize, $setting->fontNameOrPath, $setting->angle);

		$x = match ($setting->horizontal) {
			HorizontalAlignment::Left => $rectangle->left() - min($fontArea->left(), $fontArea->right()),
			HorizontalAlignment::Center  => $rectangle->left() + ($rectangle->size->width / 2) - ($fontArea->width() / 2),
			HorizontalAlignment::Right => $rectangle->right() - max($fontArea->left(), $fontArea->right()),
		};
		$y = match ($setting->vertical) {
			VerticalAlignment::Top => $rectangle->top() - min($fontArea->top(), $fontArea->bottom()),
			VerticalAlignment::Center => $rectangle->top() + ($rectangle->size->height / 2) + ($fontArea->height() / 2),
			VerticalAlignment::Bottom => $rectangle->bottom() - max($fontArea->top(), $fontArea->bottom()),
		};

		return $this->drawString(
			$text,
			$fontSize,
			new Point((int)$x, (int)$y),
			$color,
			$setting
		);
	}

	private function saveCore(ImageSetting $setting): Binary
	{
		return OutputBuffer::get(fn () => match ($setting->imageType) {
			ImageType::Png => imagepng($this->image, null, ...$setting->options()),
			ImageType::Jpeg => imagejpeg($this->image, null, ...$setting->options()),
			ImageType::Webp => imagewebp($this->image, null, ...$setting->options()),
			ImageType::Bmp => imagebmp($this->image, null, ...$setting->options()), //cspell:disable-line
			default  => throw new NotImplementedException(),
		});
	}

	/**
	 * 画像データ出力。
	 *
	 * @param ImageSetting $setting
	 * @return Binary
	 */
	public function save(ImageSetting $setting): Binary
	{
		if ($setting->imageType == ImageType::Auto) {
			throw new ArgumentException('ImageType::AUTO');
		}

		return $this->saveCore($setting);
	}

	/**
	 * 画像データをHTMLのソースとして出力。
	 *
	 * @param ImageSetting $setting
	 * @return string "data" URL scheme。
	 */
	public function saveHtmlSource(ImageSetting $setting): string
	{
		if ($setting->imageType == ImageType::Auto) {
			throw new ArgumentException('ImageType::AUTO');
		}

		$image = $this->saveCore($setting);

		$mime = match ($setting->imageType) {
			default => $setting->imageType->toMime(),
		};

		$data = 'data:' . $mime . ';base64,';
		$body = $image->toBase64();

		$result = $data . $body;

		return $result;
	}
}
