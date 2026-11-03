<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Plugin;

use Override;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class PluginIndexPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		/** @var PluginCacheItem[] $plugins */
		$plugins = $this->props->parameter->values['plugins'];
		$items = [];
		foreach ($plugins as $plugin) {
			$items[] = $b->li(new HTMLLiAttributes(), $b->a(
				new HTMLAnchorAttributes(href: '/plugin2/' . $plugin->pluginId),
				new TextNode($plugin->displayName)
			));
		}
		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: 'プラグイン',
			children: new Fragment([
				$this->props->parameter->values['link_default_plugin']
					? $b->p(new HtmlGlobalAttributes(), $b->a(
						new HTMLAnchorAttributes(href: '/management/default-plugin'),
						new TextNode('標準プラグイン')
					))
					: null,
				$b->ul(new HtmlGlobalAttributes(), new Fragment($items)),
			])
		), $this->context);
	}
}
