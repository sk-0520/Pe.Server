<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\App\Models\Domain\DefaultPlugin;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputCheckboxAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementDefaultPluginPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		/** @var array<array{item:DefaultPlugin,registered:bool}> $plugins */
		$plugins = $this->props->parameter->values['plugins'];
		$items = [];
		foreach ($plugins as $plugin) {
			$items[] = $b->li(new HTMLLiAttributes(), $this->link('/plugin/' . $plugin['item']->pluginId, ($plugin['registered'] ? '[あり] ' : '[なし] ') . $plugin['item']->pluginName));
		}
		return $this->layout('標準プラグイン', new Fragment([
			$b->ul(new HtmlGlobalAttributes(), new Fragment($items)),
			$this->form('/management/default-plugin', new Fragment([
				$b->label(new HTMLLabelAttributes(), new Fragment([
					$b->inputCheckbox(new HTMLInputCheckboxAttributes(checked: false, name: 'delete')),
					new TextNode('削除'),
				])),
				$b->button(new HTMLButtonAttributes(), new TextNode('実行')),
			])),
		]));
	}
}
