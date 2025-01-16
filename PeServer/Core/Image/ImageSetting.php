<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use PeServer\Core\Image\ImageType;

/**
 * 画像生成設定。
 */
readonly class ImageSetting
{
	/**
	 * 生成。
	 *
	 * @param ImageType $imageType
	 * @param mixed[] $options イメージ生成処理用オプション。
	 */
	public function __construct(
		public ImageType $imageType,
		protected array $options
	) {
	}

	#region function

	/**
	 * オプション。
	 *
	 * @return mixed[]
	 * @phpstan-pure
	 */
	public function options(): array
	{
		return $this->options;
	}

	public static function png(int $quality = -1, int $filters = -1): self
	{
		return new self(ImageType::Png, [$quality, $filters]);
	}

	public static function jpeg(int $quality = -1): self
	{
		return new self(ImageType::Jpeg, [$quality]);
	}

	public static function webp(int $quality = -1): self
	{
		return new self(ImageType::Webp, [$quality]);
	}

	public static function bmp(bool $compressed = true): self
	{
		return new self(ImageType::Bmp, [$compressed]);
	}

	#endregion
}
