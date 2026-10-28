<?php

declare(strict_types=1);

use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Mvc\Template\TemplateRenderer;

/**
 * @extends RootHtmlComponentBase<RootProps>
 */
class IndexPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#region RootHtmlComponentBase

	#[Override]
	protected function getNode(): INode
	{
		throw new \Exception('Not implemented');
	}

	#endregion
}
