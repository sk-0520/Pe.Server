<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Home;

use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;

/**
 * @extends RootHtmlComponentBase<RootProps>
 */
abstract class HomePageBase extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	protected function layout(string $title, INode $children): INode
	{
		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: $title,
			children: $children,
		), $this->context);
	}
}
