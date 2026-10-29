<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\Pages\Default;

use Override;
use PeServer\App\Views\Components\Asset\Asset;
use PeServer\App\Views\Components\Asset\AssetProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\CharSet;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Rel;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

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
		return $this->context->builder->html(
			new HTMLHtmlAttributes(),
			new Fragment([
				$this->context->builder->head(
					new HtmlGlobalAttributes(),
					new Fragment([
						$this->context->builder->meta(new HTMLMetaAttributes(
							charset: CharSet::Utf8,
						)),
						$this->context->builder->meta(new HTMLMetaAttributes(
							name: 'viewport',
							content: 'width=device-width, initial-scale=1.0',
						)),
						$this->context->builder->title(
							new HtmlGlobalAttributes(),
							new TextNode(
								Text::isNullOrEmpty($this->props->title)
									? 'Peサーバー'
									: $this->props->title . " - Peサーバー"
							)
						),
						$this->props->customHeadNode,
						new Asset(
							new AssetProps(
								file: '/favicon.svg',
								rel: Rel::Icon,
							),
							$this->context
						)
					]),
				),
				$this->context->builder->body(
					new HtmlGlobalAttributes(),
					new Fragment([]),
				)
			])
		);
	}
}
