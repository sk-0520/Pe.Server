<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Tool;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @extends ToolPageBase<RootProps>
 */
class ToolIndexPage extends ToolPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;

		return $this->layout('ツール', new Fragment([
			$b->p(new HtmlGlobalAttributes(), new TextNode('しょうもないツールたち。')),
			$b->p(
				new HtmlGlobalAttributes(),
				new Fragment([
					new TextNode('Pe 関係ないです。'),
					$b->br(new HTMLBrAttributes()),
					new TextNode('外部サービスを使用したくない処理を突っ込んでるだけ。'),
				])
			),
			$b->ul(new HtmlGlobalAttributes(), new Fragment([
				$b->li(new HTMLLiAttributes(), $b->a(new HTMLAnchorAttributes(href: '/tool/base64'), new TextNode('base64'))),
				$b->li(new HTMLLiAttributes(), $b->a(new HTMLAnchorAttributes(href: '/tool/json'), new TextNode('json'))),
			])),
		]));
	}
}
