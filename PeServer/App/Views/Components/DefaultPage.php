<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;

/**
 * @extends HtmlComponentBase<\PeServer\App\Views\Components\DefaultPageProps>
 */
class DefaultPage extends HtmlComponentBase
{
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		return $this->builder->html(
			new HTMLHtmlAttributes(),
			new Fragment([
				$this->builder->head(
					new HtmlGlobalAttributes(),
					new Fragment([]),
				),
				$this->builder->body(
					new HtmlGlobalAttributes(),
					new Fragment([]),
				)
			])
		);
	}
}
