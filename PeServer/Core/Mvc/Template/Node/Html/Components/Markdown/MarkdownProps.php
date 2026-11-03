<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\Markdown;

use PeServer\Core\Mvc\Template\Node\Props;

readonly class MarkdownProps extends Props
{
	public function __construct(
		public string $content,
		public string $className = 'markdown',
		public bool $safeMode = true,
	) {
		//NOP
	}
}
