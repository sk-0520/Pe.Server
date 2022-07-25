<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

class ImageOption
{
	/**
	 * 生成。
	 *
	 * @param mixed[] $options イメージ生成処理用オプション。
	 */
	public function __construct(
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
		return new ImageOption([$quality, $filters]);
	}

	public static function jpeg(int $quality = -1): ImageOption
	{
		return new ImageOption([$quality]);
	}

	public static function webp(int $quality = -1): ImageOption
	{
		return new ImageOption([$quality]);
	}

	public static function bmp(bool $compressed = true): ImageOption
	{
		return new ImageOption([$compressed]);
	}
}
