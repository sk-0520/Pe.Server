<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\Environment;
use PeServer\Core\Store\Stores;

class RawPhpTemplate extends TemplateBase
{

	public function __construct(
		TemplateOptions $options,
		protected Stores $stores,
		protected Environment $environment
	) {
		parent::__construct($options);
	}

	#region TemplateBase

	public function build(string $templateName, TemplateParameter $parameter): string
	{
		return "";
	}

	#endregion
}
