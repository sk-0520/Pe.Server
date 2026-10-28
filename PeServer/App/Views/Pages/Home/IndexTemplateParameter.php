<?php

declare(strict_types=1);

use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\App\Views\Pages\Home\IndexPageProps;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Mvc\Template\TemplateRenderer;
use PeServer\Core\Http\HttpStatus;

class IndexTemplateParameter extends NodeTemplateParameter
{
	public function __construct(
		HttpStatus $httpStatus
	) {
		parent::__construct($httpStatus);
	}
}
