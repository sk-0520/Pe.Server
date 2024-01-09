<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplateEngine;

use PeServer\Core\Encoding;

interface ITemplateVariableFilter
{
	#region

	public function getEncoding(): Encoding;

	public function filter(mixed $value): string;

	#endregion
}
