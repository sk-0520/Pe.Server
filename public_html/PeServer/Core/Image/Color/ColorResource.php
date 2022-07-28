<?php

declare(strict_types=1);

namespace PeServer\Core\Image\Color;

use PeServer\Core\Code;
use PeServer\Core\DisposerBase;
use PeServer\Core\Image\Graphics;
use PeServer\Core\Image\Color\IColor;
use PeServer\Core\StringUtility;

class ColorResource extends DisposerBase implements IColor
{
	public function __construct(
		private Graphics $graphics,
		public int $value
	) {
	}

	protected function disposeImpl(): void
	{
		$this->graphics->detachColor($this);

		parent::disposeImpl();
	}

	public function toHtml(): string
	{
		$colors = imagecolorsforindex($this->graphics->image, $this->value);

		if (isset($colors['alpha']) && $colors['alpha'] === IColor::ALPHA_NONE) {
			return StringUtility::format('#%02x%02x%02x', $colors['red'], $colors['green'], $colors['blue']);
		}

		return StringUtility::format('#%02x%02x%02x%02x', $colors['red'], $colors['green'], $colors['blue'], $colors['alpha']);
	}

	public function __toString(): string
	{
		return Code::toString($this, (string)$this->value);
	}
}
