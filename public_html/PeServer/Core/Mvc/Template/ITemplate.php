<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

interface ITemplate
{
	function build(string $templateName, TemplateParameter $parameter): string;
}
