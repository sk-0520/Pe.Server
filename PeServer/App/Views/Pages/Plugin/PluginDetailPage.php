<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Plugin;

use Override;
use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\Markdown;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\MarkdownProps;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class PluginDetailPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		/** @var PluginCacheItem $plugin */
		$plugin = $this->props->parameter->values['plugin'];
		/** @var PluginCacheCategory[] $categories */
		$categories = $this->props->parameter->values['categories'];
		$items = [];
		foreach ($categories as $category) {
			$items[] = $b->li(new HTMLLiAttributes(), new TextNode($category->categoryName));
		}
		$nodes = [
			$b->dt(new HtmlGlobalAttributes(), new TextNode('カテゴリ')),
			$b->dd(new HtmlGlobalAttributes(), empty($categories)
				? $b->span(new HtmlGlobalAttributes(className: 'mute'), new TextNode('プラグインカテゴリ未登録'))
				: $b->ul(new HtmlGlobalAttributes(className: 'inline'), new Fragment($items))),
		];
		foreach (['プラグインID' => $plugin->pluginId, 'プラグイン内部名' => $plugin->pluginName] as $label => $value) {
			$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode($label));
			$nodes[] = $b->dd(new HtmlGlobalAttributes(), $b->code(
				new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'inline'])),
				new TextNode($value)
			));
		}
		$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode('プラグイン表示名'));
		$nodes[] = $b->dd(new HtmlGlobalAttributes(), new TextNode($plugin->displayName));
		$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode('状態'));
		$nodes[] = $b->dd(new HtmlGlobalAttributes(), new TextNode(PluginState::toString($plugin->state)));
		$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode('説明'));
		$nodes[] = $b->dd(new HtmlGlobalAttributes(), new Markdown(new MarkdownProps($plugin->description), $this->context));

		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: 'プラグイン: ' . $plugin->displayName,
			children: $b->dl(new HtmlGlobalAttributes(), new Fragment($nodes))
		), $this->context);
	}
}
