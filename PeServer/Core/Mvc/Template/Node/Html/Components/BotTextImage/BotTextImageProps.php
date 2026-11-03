<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\BotTextImage;

use PeServer\Core\Mvc\Template\Node\Props;

readonly class BotTextImageProps extends Props
{
	public function __construct(
		public string $text,
		public string $alt = '',
		/** @var positive-int */
		public int $width = 100,
		/** @var positive-int */
		public int $height = 100,
		public float $fontSize = 12.5,
		public string $backgroundColor = '#eeeeee',
		public string $foregroundColor = '#0f0f0f',
		public string $className = '',
	) {
		//NOP
	}
}
