<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Text;

readonly class TextNodeOptions
{
	public function __construct(
		public string $newline = PHP_EOL
	) {
		//NOP
	}
}
