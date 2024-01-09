<?php

declare(strict_types=1);

namespace PeServer\Core\TemplateEngine;

interface ITemplateVariableFilter
{
	#region

	public function filter(mixed $value): string;

	#endregion
}
