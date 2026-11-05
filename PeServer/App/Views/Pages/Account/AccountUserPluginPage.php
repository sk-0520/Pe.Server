<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\App\Models\Data\Dto\PluginCategoryDto;
use PeServer\App\Models\Domain\PluginState;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLabelAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputCheckboxAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputHiddenAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputRadioAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ButtonType;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\Components\MarkdownEditor\MarkdownEditor;
use PeServer\Core\Mvc\Template\Node\Html\Components\MarkdownEditor\MarkdownEditorProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessages;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends AccountPageBase<TRootProps>
 */
class AccountUserPluginPage extends AccountPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$p = $this->props->parameter;
		$v = $p->values;
		$register = empty($v['from_account_plugin_plugin_id']);
		$nodes = [
			$b->dt(new HtmlGlobalAttributes(), new Fragment([
				new TextNode('プラグインID'),
				$register ? $b->ul(new HtmlGlobalAttributes(className: 'helper'), $b->li(
                    new HTMLLiAttributes(),
                    $b->button(new HTMLButtonAttributes(id: 'pg-plugin-id-auto-generate', className: 'action sub', type: ButtonType::Button), new TextNode('自動生成'))
                )) : null,
			])),
			$b->dd(new HtmlGlobalAttributes(), new InputHelper(new InputHelperProps(
				parameter: $p,
                key: 'account_plugin_plugin_id',
                type: InputHelperType::Text,
				className: 'edit',
                id: 'pg-plugin-id',
                readOnly: !$register,
			), $this->context)),
			$this->field('プラグイン内部名', new InputHelperProps(
				parameter: $p,
                key: 'account_plugin_plugin_name',
                type: InputHelperType::Text,
				className: 'edit',
                readOnly: !$register,
			)),
			$this->input('プラグイン表示名', 'account_plugin_display_name', InputHelperType::Text),
			$b->dt(new HtmlGlobalAttributes(), new TextNode('状態')),
		];
		/** @var string[] $states */
		$states = $v['plugin_state_items'];
		/** @var string[] $editableStates */
		$editableStates = $v['plugin_state_editable_items'];
		$stateItems = [];
		foreach ($states as $state) {
			$stateItems[] = $b->li(new HTMLLiAttributes(), $b->label(new HTMLLabelAttributes(), new Fragment([
				$b->inputRadio(new HTMLInputRadioAttributes(
					name: 'account_plugin_state',
                    value: $state,
					disabled: !in_array($state, $editableStates),
					checked: $state === $v['account_plugin_state'],
				)),
				new TextNode(PluginState::toString($state)),
			])));
		}
		$nodes[] = $b->dd(new HtmlGlobalAttributes(), new Fragment([
			$b->ul(new HtmlGlobalAttributes(className: 'inline'), new Fragment($stateItems)),
			new ShowErrorMessages(new ShowErrorMessagesProps($p->errors, 'account_plugin_state'), $this->context),
		]));
		$nodes[] = $this->input('バージョンチェックURL', 'account_plugin_check_url', InputHelperType::Url);
		$nodes[] = $this->input('紹介ページURL', 'account_plugin_lp_url', InputHelperType::Url);
		$nodes[] = $this->input('プロジェクトURL', 'account_plugin_project_url', InputHelperType::Url);
		$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode('説明'));
		$nodes[] = $b->dd(new HtmlGlobalAttributes(), new MarkdownEditor(new MarkdownEditorProps($p, 'account_plugin_description'), $this->context));
		/** @var PluginCategoryDto[] $categories */
		$categories = $v['plugin_categories'];
		/** @var string[] $mappings */
		$mappings = $v['plugin_category_mappings'];
		$categoryItems = [];
		foreach ($categories as $category) {
			$categoryItems[] = $b->li(new HTMLLiAttributes(), $b->label(new HTMLLabelAttributes(), new Fragment([
				$b->inputCheckbox(new HTMLInputCheckboxAttributes(
					name: 'plugin_category_' . $category->pluginCategoryId,
					checked: in_array($category->pluginCategoryId, $mappings, true),
				)),
				new TextNode($category->displayName),
			])));
		}
		$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode('カテゴリー'));
		$nodes[] = $b->dd(new HtmlGlobalAttributes(), $b->ul(new HtmlGlobalAttributes(), new Fragment($categoryItems)));
		$nodes[] = $this->submit('登録');
		return $this->layout(
			$register ? 'プラグイン 登録' : 'プラグイン 編集: ' . Text::toString($v['account_plugin_plugin_name']),
			$this->form('/account/user/plugin' . ($register ? '' : '/' . Text::toString($v['from_account_plugin_plugin_id'])), 'page-account-plugin', new Fragment([
				!$register ? $b->inputHidden(new HTMLInputHiddenAttributes(
					name: 'from_account_plugin_plugin_id',
                    value: Text::toString($v['from_account_plugin_plugin_id'])
				)) : null,
				$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment($nodes)),
			])),
			'/scripts/plugin_edit.js'
		);
	}
}
