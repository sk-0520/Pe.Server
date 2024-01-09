<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplateEngine;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\TypeUtility;

final class TemplatePlainVariableFilter extends TemplateVariableFilterBase
{
	public function __construct(
		?Encoding $encoding = null
	) {
		parent::__construct($encoding);
	}

	#region TemplateVariableFilterBase

	public function filter(mixed $value): string
	{
		return $this->getEncoding()->toString(new Binary(TypeUtility::toString($value)));
	}

	#endregion
}
