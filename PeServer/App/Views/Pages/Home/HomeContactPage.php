<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Home;

use Override;
use PeServer\App\Models\Configuration\ProjectFamilySetting;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @extends HomePageBase<RootProps>
 */
class HomeContactPage extends HomePageBase
{
	#[Override]
	protected function getNode(): INode
	{
		/** @var ProjectFamilySetting $families */
		$families = $this->props->parameter->values['families'];
		$serverProjectUrl = Text::isNullOrEmpty($families->serverProjectUrl) ? null : $families->serverProjectUrl;
		$appProjectUrl = Text::isNullOrEmpty($families->appProjectUrl) ? null : $families->appProjectUrl;
		$b = $this->context->builder;

		return $this->layout('問い合わせ', new Fragment([
			$b->p(
				new HtmlGlobalAttributes(),
				new TextNode('問い合わせフォームは作る気力なくて課題の方へどうぞ。')
			),
			$b->ul(
				new HtmlGlobalAttributes(),
				new Fragment([
					$b->li(
						new HTMLLiAttributes(),
						$b->a(new HTMLAnchorAttributes(href: $serverProjectUrl), new TextNode('Pe.Server プロジェクト'))
					),
					$b->li(
						new HTMLLiAttributes(),
						$b->a(new HTMLAnchorAttributes(href: $appProjectUrl), new TextNode('Pe プロジェクト'))
					),
				])
			),
		]));
	}
}
