<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplateEngine;

class TemplateFile
{
	public function __construct(
		public string $path
	) {
	}
}
