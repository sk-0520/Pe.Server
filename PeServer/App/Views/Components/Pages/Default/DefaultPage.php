<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\Pages\Default;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\CharSet;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;

/**
 * @extends HtmlComponentBase<\PeServer\App\Views\Components\Pages\Default\DefaultPageProps>
 */
class DefaultPage extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<\PeServer\App\Views\Components\Pages\Default\DefaultPageProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		return $this->html->html(
			new HTMLHtmlAttributes(),
			new Fragment([
				$this->html->head(
					new HtmlGlobalAttributes(),
					new Fragment([
						$this->html->meta(new HTMLMetaAttributes(
							charset: CharSet::Utf8,
						)),
						$this->html->meta(new HTMLMetaAttributes(
							name: 'viewport',
							content: 'width=device-width, initial-scale=1.0',
						)),
						$this->props->customHeadNode,

						$this->html->title(new HtmlGlobalAttributes(), new Fragment([])),
					]),
				),
				$this->html->body(
					new HtmlGlobalAttributes(),
					new Fragment([]),
				)
			])
		);
	}
}
