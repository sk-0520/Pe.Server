<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\App\Models\Data\Dto\PluginCategoryDto;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputTextAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementPluginCategoryPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		/** @var list<PluginCategoryDto> $categories */
		$categories = $this->props->parameter->values['categories'];
		$headers = [];
		foreach (['カテゴリID', 'カテゴリ名', '説明', '更新', '削除'] as $label) {
			$headers[] = $b->th(new HTMLTableHeaderCellAttributes(), new TextNode($label));
		}
		$rows = [];
		foreach ($categories as $category) {
			$rows[] = $b->tr(new HtmlGlobalAttributes(data: new DataAttributes(['plugin-category-id' => $category->pluginCategoryId])), new Fragment([
				$b->td(new HTMLTableCellAttributes(), $b->inputText(new HTMLInputTextAttributes(className: 'edit', readOnly: true, value: $category->pluginCategoryId))),
				$b->td(new HTMLTableCellAttributes(), $b->inputText(new HTMLInputTextAttributes(name: 'display-name', className: 'edit', value: $category->displayName))),
				$b->td(new HTMLTableCellAttributes(), $b->inputText(new HTMLInputTextAttributes(name: 'description', className: 'edit', value: $category->description))),
				$b->td(new HTMLTableCellAttributes(), $b->button(new HTMLButtonAttributes(name: 'update'), new TextNode('📝'))),
				$b->td(new HTMLTableCellAttributes(), $b->button(new HTMLButtonAttributes(name: 'delete'), new TextNode('❌'))),
			]));
		}
		return $this->layout('プラグインカテゴリ', $b->table(new HtmlGlobalAttributes(), new Fragment([
			$b->thead(new HtmlGlobalAttributes(), $b->tr(new HtmlGlobalAttributes(), new Fragment($headers))),
			$b->tbody(new HtmlGlobalAttributes(id: 'category_items'), new Fragment($rows)),
			$b->tfoot(new HtmlGlobalAttributes(), $b->tr(new HtmlGlobalAttributes(), new Fragment([
				$b->td(new HTMLTableCellAttributes(), $b->inputText(new HTMLInputTextAttributes(id: 'category_add_id', className: 'edit', placeholder: '一意なカテゴリID'))),
				$b->td(new HTMLTableCellAttributes(), $b->inputText(new HTMLInputTextAttributes(id: 'category_add_display', className: 'edit'))),
				$b->td(new HTMLTableCellAttributes(), $b->inputText(new HTMLInputTextAttributes(id: 'category_add_description', className: 'edit'))),
				$b->td(new HTMLTableCellAttributes(colSpan: 2), $b->button(new HTMLButtonAttributes(id: 'category_add_submit'), new TextNode('✅追加'))),
			]))),
		])), '/scripts/management_plugin_category.js', new Csrf(new CsrfProps('id'), $this->context));
	}
}
