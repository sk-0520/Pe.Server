<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\Mvc\Template\TemplateOptions;

interface ITemplateFactory
{
	#region function

	function createTemplate(TemplateOptions $options): TemplateBase;

	#endregion
}
