<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Home;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @extends HomePageBase<RootProps>
 */
class HomeAboutPage extends HomePageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;

		return $this->layout('問い合わせ', $b->ul(
			new HtmlGlobalAttributes(),
			new Fragment([
				$b->li(new HTMLLiAttributes(), $b->a(new HTMLAnchorAttributes(href: '/about/privacy'), new TextNode('プライバシーポリシー'))),
				$b->li(new HTMLLiAttributes(), $b->a(new HTMLAnchorAttributes(href: '/about/contact'), new TextNode('お問い合わせ'))),
			])
		));
	}
}
