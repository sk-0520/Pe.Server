<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\Code;

use PeServer\Core\Mvc\Template\Node\Props;

readonly class CodeProps extends Props
{
	public function __construct(
		public string $content,
		public string $language = '',
		public string $numbers = '',
	) {
		//NOP
	}
}
