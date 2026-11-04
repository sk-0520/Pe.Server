<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Home;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

class HomeApiPage extends HomePageBase
{
	#[Override]
	protected function getNode(): INode
	{
		/** @var string $apiDocUrl */
		$apiDocUrl = $this->props->parameter->values['api_doc_url'];
		$apiDocUrl = Text::isNullOrEmpty($apiDocUrl) ? null : $apiDocUrl;
		$b = $this->context->builder;

		return $this->layout('API', new Fragment([
			$b->section(
				new HtmlGlobalAttributes(),
				new Fragment([
					$b->h2(new HtmlGlobalAttributes(), new TextNode('APIドキュメント')),
					$b->p(new HtmlGlobalAttributes(), $b->a(new HTMLAnchorAttributes(href: $apiDocUrl), new TextNode('Pe.Server'))),
					$b->p(
						new HtmlGlobalAttributes(),
						new Fragment([
							new TextNode('上記APIドキュメント最新版に従う。'),
							$b->br(new HTMLBrAttributes()),
							new TextNode('次期開発版デプロイ前に最新化される可能性がよくある。'),
						])
					),
				])
			),
			$b->section(
				new HtmlGlobalAttributes(),
				new Fragment([
					$b->h2(new HtmlGlobalAttributes(), new TextNode('共通事項')),
					$b->table(
						new HtmlGlobalAttributes(),
						$b->tbody(
							new HtmlGlobalAttributes(),
							new Fragment([
								$b->tr(new HtmlGlobalAttributes(), new Fragment([
									$b->th(new HTMLTableHeaderCellAttributes(), new TextNode('X-API-KEY')),
									$b->td(new HTMLTableCellAttributes(), new TextNode('APIキー')),
									$b->td(new HTMLTableCellAttributes(), new TextNode('ユーザーページのAPIから取得')),
								])),
								$b->tr(new HtmlGlobalAttributes(), new Fragment([
									$b->th(new HTMLTableHeaderCellAttributes(), new TextNode('X-SECRET-KEY')),
									$b->td(new HTMLTableCellAttributes(), new TextNode('シークレットキー')),
									$b->td(new HTMLTableCellAttributes(), new TextNode('API生成時にのみ出力される')),
								])),
							])
						)
					),
				])
			),
		]));
	}
}
