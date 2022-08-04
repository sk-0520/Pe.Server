<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

class ImageOption
{
	/**
	 * 生成。
	 *
	 * @param int $imageType
	 * @phpstan-param ImageType::* $imageType
	 * @param mixed[] $options イメージ生成処理用オプション。
	 */
	public function __construct(
		public int $imageType,
		protected array $options
	) {
	}

	/**
	 * オプション。
	 *
	 * @return mixed[]
	 */
	public function options(): array
	{
		return $this->options;
	}

	public static function png(int $quality = -1, int $filters = -1): ImageOption
	{
		return new ImageOption(ImageType::PNG, [$quality, $filters]);
	}

	public static function jpeg(int $quality = -1): ImageOption
	{
		return new ImageOption(ImageType::PNG, [$quality]);
	}

	public static function webp(int $quality = -1): ImageOption
	{
		return new ImageOption(ImageType::PNG, [$quality]);
	}

	public static function bmp(bool $compressed = true): ImageOption
	{
		return new ImageOption(ImageType::PNG, [$compressed]);
	}
}
