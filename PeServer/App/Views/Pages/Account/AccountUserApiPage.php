<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableCellAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTableHeaderCellAttributes;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends AccountPageBase<TRootProps>
 */
class AccountUserApiPage extends AccountPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$v = $this->props->parameter->values;
		$register = $v['from_account_api_is_register'];
		$rows = [];
		if (!$register) {
			$rows[] = $b->tr(new HtmlGlobalAttributes(), new Fragment([
				$b->td(new HTMLTableCellAttributes(), $b->pre(new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'block'])), new TextNode(Text::toString($v['api_key'])))),
				$b->td(new HTMLTableCellAttributes(), new TextNode(Text::toString($v['created_timestamp']))),
			]));
			if (!empty($v['secret_key'])) {
				$rows[] = $b->tr(
                    new HtmlGlobalAttributes(),
                    $b->th(new HTMLTableHeaderCellAttributes(colSpan: 2), new TextNode('シークレットキー'))
                );
				$rows[] = $b->tr(new HtmlGlobalAttributes(), $b->td(
                    new HTMLTableCellAttributes(colSpan: 2),
                    $b->pre(new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'block'])), new TextNode(Text::toString($v['secret_key'])))
                ));
				$rows[] = $b->tr(new HtmlGlobalAttributes(), $b->td(
                    new HTMLTableCellAttributes(colSpan: 2),
                    $b->strong(new HtmlGlobalAttributes(), new TextNode('シークレットキーは再表示できません。後生大事に保持しておいてください。'))
                ));
			}
		}
		$keyNode = $register
			? $b->p(new HtmlGlobalAttributes(className: 'mute'), new TextNode('APIキーは登録されていません。'))
			: $b->table(new HtmlGlobalAttributes(), new Fragment([
				$b->thead(new HtmlGlobalAttributes(), $b->tr(new HtmlGlobalAttributes(), new Fragment([
					$b->th(new HTMLTableHeaderCellAttributes(), new TextNode('APIキー')),
					$b->th(new HTMLTableHeaderCellAttributes(), new TextNode('作成日')),
				]))),
				$b->tbody(new HtmlGlobalAttributes(), new Fragment($rows)),
			]));
		return $this->layout($register ? 'APIキー登録' : 'APIキー確認', $this->form(
            '/account2/user/api',
            'page-account-api',
			$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
				$b->dt(new HtmlGlobalAttributes(), new TextNode('説明')),
				$b->dd(new HtmlGlobalAttributes(), $b->p(new HtmlGlobalAttributes(), new TextNode('APIキーを用いてAPIを実行することができます。'))),
				$b->dt(new HtmlGlobalAttributes(), new TextNode('APIキー')),
				$b->dd(new HtmlGlobalAttributes(), $keyNode),
				$this->submit($register ? '登録' : '削除'),
			]))
		));
	}
}
