<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormMethod;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class AccountSignupStep2Page extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$parameter = $this->props->parameter;
		$fields = [
			['メールアドレス', 'account_signup_email', InputHelperType::Email],
			['ログインID', 'account_signup_login_id', InputHelperType::Text],
			['パスワード', 'account_signup_password', InputHelperType::Password],
			['パスワード(確認用)', 'account_signup_password_confirm', InputHelperType::Password],
			['名前', 'account_signup_name', InputHelperType::Text],
		];
		$nodes = [];
		foreach ($fields as [$label, $key, $type]) {
			$nodes[] = $this->context->builder->dt(new HtmlGlobalAttributes(), new TextNode($label));
			$nodes[] = $this->context->builder->dd(new HtmlGlobalAttributes(), new InputHelper(new InputHelperProps(
				parameter: $parameter,
				key: $key,
				type: $type,
				className: 'edit',
				required: true,
			), $this->context));
		}
		$nodes[] = $this->context->builder->dt(new HtmlGlobalAttributes(className: 'action'), NoneNode::get());
		$nodes[] = $this->context->builder->dd(
            new HtmlGlobalAttributes(className: 'action'),
            $this->context->builder->button(new HTMLButtonAttributes(), new TextNode('登録'))
        );

		return new DefaultPage(new DefaultPageProps(
			parameter: $parameter,
			title: 'ユーザー登録 2/2',
			children: $this->context->builder->form(
				new HTMLFormAttributes(
					action: '/account/signup2/' . Text::toString($parameter->values['token']),
					method: FormMethod::Post,
					className: 'page-account-sign-up',
				),
				new Fragment([
					new Csrf(new CsrfProps(), $this->context),
					$this->context->builder->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment($nodes)),
				])
			)
		), $this->context);
	}
}
