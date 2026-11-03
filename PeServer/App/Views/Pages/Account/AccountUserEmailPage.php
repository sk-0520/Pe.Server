<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLTimeAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\DateTimeValueBase;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputHiddenAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends AccountPageBase<TRootProps>
 */
class AccountUserEmailPage extends AccountPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$v = $this->props->parameter->values;
		$nodes = [
			$b->section(new HtmlGlobalAttributes(className: 'page-account-email-edit'), new Fragment([
				$b->h2(new HtmlGlobalAttributes(), new TextNode('変更前処理')),
				$b->p(new HtmlGlobalAttributes(), new TextNode('変更先メールアドレスに変更用トークンを送信します。')),
				$this->form('/account/user/email2', '', new Fragment([
					$b->inputHidden(new HTMLInputHiddenAttributes(name: 'account_email_mode', value: 'edit')),
					$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
						$this->input('変更先メールアドレス', 'account_email_email', InputHelperType::Email, true),
						$this->submit('送信'),
					])),
				])),
			])),
		];
		if (!empty($v['wait_email'])) {
			$nodes[] = $b->section(new HtmlGlobalAttributes(className: 'page-account-email-confirm'), new Fragment([
				$b->h2(new HtmlGlobalAttributes(), new TextNode('変更後処理')),
				$b->p(new HtmlGlobalAttributes(), new TextNode('変更用トークン入力待ちです。')),
				$this->form('/account/user/email2', '', new Fragment([
					$b->inputHidden(new HTMLInputHiddenAttributes(name: 'account_email_mode', value: 'confirm')),
					$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
						$b->dt(new HtmlGlobalAttributes(), new TextNode('待機中メールアドレス')),
						$b->dd(new HtmlGlobalAttributes(), $b->code(
							new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'inline'])),
							new TextNode(Text::toString($v['wait_email']))
						)),
						$b->dt(new HtmlGlobalAttributes(), new TextNode('発行日時')),
						$b->dd(new HtmlGlobalAttributes(), $b->time(
							new HTMLTimeAttributes(dateTime: new DateTimeValueBase(Text::toString($v['token_timestamp_utc']), 'Y-m-d\TH:i:s\Z')),
							new TextNode(Text::toString($v['token_timestamp_utc']))
						)),
						$this->input('変更用トークン', 'account_email_token', InputHelperType::Text, true),
						$this->submit('変更'),
					])),
				])),
			]));
		}
		return $this->layout('メールアドレス変更', new Fragment($nodes));
	}
}
