<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputHiddenAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputPasswordAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputTextAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormMethod;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessages;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps;
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
class AccountLoginPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	private function getFieldErrors(string $key): INode
	{
		return new ShowErrorMessages(
			new ShowErrorMessagesProps($this->props->parameter->errors, $key),
			$this->context
		);
	}

	private function getDevelopmentLogin(string $loginId, string $password, string $label, string $style): INode
	{
		return $this->context->builder->form(
			new HTMLFormAttributes(action: '/account/login2', method: FormMethod::Post, style: $style),
			new Fragment([
				new Csrf(new CsrfProps(), $this->context),
				$this->context->builder->inputHidden(new HTMLInputHiddenAttributes(
					name: 'account_login_login_id',
					value: $loginId
				)),
				$this->context->builder->inputHidden(new HTMLInputHiddenAttributes(
					name: 'account_login_password',
					value: $password
				)),
				$this->context->builder->button(
					new HTMLButtonAttributes(className: 'link', data: new DataAttributes(['dialog' => 'disabled'])),
					new TextNode($label)
				)
			])
		);
	}

	#[Override]
	protected function getNode(): INode
	{
		$values = $this->props->parameter->values;
		$errors = $this->props->parameter->errors;

		return new DefaultPage(
			new DefaultPageProps(
				parameter: $this->props->parameter,
				title: 'ログイン',
				children: new Fragment([
					$this->context->builder->form(
						new HTMLFormAttributes(
							action: '/account/login2',
							method: FormMethod::Post,
							className: 'page-account-login'
						),
						new Fragment([
							new Csrf(new CsrfProps(), $this->context),
							$this->context->builder->dl(
								new HtmlGlobalAttributes(className: 'input'),
								new Fragment([
									$this->context->builder->dt(
										new HtmlGlobalAttributes(),
										new TextNode('ログインID')
									),
									$this->context->builder->dd(
										new HtmlGlobalAttributes(),
										new Fragment([
											$this->context->builder->inputText(new HTMLInputTextAttributes(
												id: 'account_login_login_id',
												name: 'account_login_login_id',
												value: Text::toString($values['account_login_login_id'] ?? Text::EMPTY),
												className: empty($errors['account_login_login_id']) ? 'edit' : 'edit error',
												autoFocus: true,
												required: true
											)),
											$this->getFieldErrors('account_login_login_id')
										])
									),
									$this->context->builder->dt(
										new HtmlGlobalAttributes(),
										new TextNode('パスワード')
									),
									$this->context->builder->dd(
										new HtmlGlobalAttributes(),
										new Fragment([
											$this->context->builder->inputPassword(new HTMLInputPasswordAttributes(
												id: 'account_login_password',
												name: 'account_login_password',
												value: Text::toString($values['account_login_password'] ?? Text::EMPTY),
												className: empty($errors['account_login_password']) ? 'edit' : 'edit error',
												required: true
											)),
											$this->getFieldErrors('account_login_password')
										])
									),
									$this->context->builder->dt(
										new HtmlGlobalAttributes(className: 'action'),
										NoneNode::get()
									),
									$this->context->builder->dd(
										new HtmlGlobalAttributes(className: 'action'),
										$this->context->builder->button(
											new HTMLButtonAttributes(),
											new TextNode('ログイン')
										)
									),
									$this->context->builder->dd(
										new HtmlGlobalAttributes(className: 'password-reminder'),
										$this->context->builder->p(
											new HtmlGlobalAttributes(),
											$this->context->builder->a(
												new HTMLAnchorAttributes(href: '/password/reminder'),
												new TextNode('パスワードを忘れた場合はこちら')
											)
										)
									)
								])
							)
						])
					),
					$this->context->environment->isDevelopment()
						? new Fragment([
							$this->getDevelopmentLogin('root', 'root', '開発ログイン(管理)', 'text-align: center; margin-top: 4em'),
							$this->getDevelopmentLogin('user', 'user', '開発ログイン(通常)', 'text-align: center;')
						])
						: null
				])
			),
			$this->context
		);
	}
}
