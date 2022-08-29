<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

interface ITemplateFactory
{
	#region function

	function createTemplate(TemplateOptions $options): TemplateBase;

	#endregion
}
