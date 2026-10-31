<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Home;

use Override;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Mvc\Template\TemplateRenderer;

/**
 * @template TRootProps of \PeServer\Core\Mvc\Template\Node\RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class IndexPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#region RootHtmlComponentBase

	#[Override]
	protected function getNode(): INode
	{
		return new DefaultPage(
			new DefaultPageProps(
				parameter: $this->props->parameter
			),
			$this->context
		);
	}

	#endregion
}
