<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

interface ITemplateFactory
{
	function createTemplate(TemplateOptions $options): TemplateBase;
}
