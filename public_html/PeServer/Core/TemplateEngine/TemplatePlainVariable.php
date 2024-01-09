<?php

declare(strict_types=1);

namespace PeServer\Core\TemplateEngine;

use PeServer\Core\TypeUtility;

final class TemplateVariableFilter implements ITemplateVariableFilter
{
	#region

	public function filter(mixed $value): string
	{
		return TypeUtility::toString($value);
	}

	#endregion
}
