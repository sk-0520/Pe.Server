<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplateEngine;

use PeServer\Core\Encoding;
use PeServer\Core\TypeUtility;

class TemplateHtmlVariable extends TemplateVariableFilterBase
{
	public function __construct(
		Encoding $encoding = null
	) {
		parent::__construct($encoding);
	}

	#region

	public function filter(mixed $value): string
	{
		$s = TypeUtility::toString($value);
		return htmlspecialchars($s, ENT_QUOTES, $this->getEncoding()->name);
	}

	#endregion
}
