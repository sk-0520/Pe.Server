<?php

declare(strict_types=1);

namespace PeServer\Core\TemplateEngine;

use PeServer\Core\Encoding;
use PeServer\Core\TypeUtility;

abstract class TemplateVariableFilterBase implements ITemplateVariableFilter
{
	private readonly Encoding $encoding;

	public function __construct(
		?Encoding $encoding = null
	) {
		$this->encoding = $encoding ?? Encoding::getDefaultEncoding();
	}

	#region ITemplateVariableFilter

	final public function getEncoding(): Encoding
	{
		return $this->encoding;
	}

	#endregion
}
