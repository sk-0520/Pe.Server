<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLiAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ButtonType;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends AccountPageBase<TRootProps>
 */
class AccountUserPluginReservePage extends AccountPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		return $this->layout('プラグイン 予約', $this->form(
            '/account/user/plugin/reserve',
            'page-account-plugin-reserve',
			$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
				$b->dt(new HtmlGlobalAttributes(), new Fragment([
					new TextNode('プラグインID'),
					$b->ul(new HtmlGlobalAttributes(className: 'helper'), $b->li(
                        new HTMLLiAttributes(),
                        $b->button(new HTMLButtonAttributes(id: 'pg-plugin-id-auto-generate', className: 'action sub', type: ButtonType::Button), new TextNode('自動生成'))
                    )),
				])),
				$b->dd(new HtmlGlobalAttributes(), new InputHelper(new InputHelperProps(
					parameter: $this->props->parameter,
					key: 'account_plugin_reserve_plugin_id',
					type: InputHelperType::Text,
					className: 'edit',
					id: 'pg-plugin-id',
				), $this->context)),
				$this->input('プラグイン内部名', 'account_plugin_reserve_plugin_name', InputHelperType::Text),
				$this->submit('登録'),
			]))
		), '/scripts/plugin_edit.js');
	}
}
