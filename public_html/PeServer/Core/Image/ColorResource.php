<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

use PeServer\Core\Code;
use PeServer\Core\DisposerBase;
use PeServer\Core\IDisposable;
use PeServer\Core\Image\IColor;

class ColorResource extends DisposerBase implements IColor
{
	public function __construct(
		private Graphics $graphics,
		public int $value
	) {
	}

	protected function disposeImpl(): void
	{
		parent::disposeImpl();

		$this->graphics->detachColor($this);
	}

	public function __toString(): string
	{
		return Code::toString($this, (string)$this->value);
	}
}
