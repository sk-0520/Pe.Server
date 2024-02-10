<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\Mvc\Template\TemplateParameter;

interface ITemplate
{
	#region function

	public function build(string $templateName, TemplateParameter $parameter): string;

	#endregion
}
