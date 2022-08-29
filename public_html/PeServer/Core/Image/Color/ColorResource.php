<?php

declare(strict_types=1);

namespace PeServer\Core\Image\Color;

use PeServer\Core\Code;
use PeServer\Core\DisposerBase;
use PeServer\Core\Image\Graphics;
use PeServer\Core\Image\Color\IColor;
use PeServer\Core\Text;

/**
 * GD: 色データ。
 */
class ColorResource extends DisposerBase implements IColor
{
	public function __construct(
		private Graphics $graphics,
		public int $value
	) {
	}

	#region function

	/**
	 * RGBへ変換。
	 *
	 * @return RgbColor
	 */
	public function toRgb(): RgbColor
	{
		$colors = imagecolorsforindex($this->graphics->image, $this->value);

		if (isset($colors['alpha']) && $colors['alpha'] === IColor::ALPHA_NONE) {
			return new RgbColor($colors['red'], $colors['green'], $colors['blue']);
		}

		return new RgbColor($colors['red'], $colors['green'], $colors['blue'], $colors['alpha']);
	}

	#endregion

	#region IColor

	public function toHtml(): string
	{
		$color = $this->toRgb();
		return $color->toHtml();
	}

	public function __toString(): string
	{
		return Code::toString($this, (string)$this->value);
	}

	#endregion

	#region DisposerBase

	protected function disposeImpl(): void
	{
		$this->graphics->detachColor($this);

		parent::disposeImpl();
	}

	#endregion
}
