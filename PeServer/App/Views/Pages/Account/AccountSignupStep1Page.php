<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Account;

use Override;
use PeServer\App\Models\AppEmailInformation;
use PeServer\App\Views\Components\EmailInformation\EmailInformation;
use PeServer\App\Views\Components\EmailInformation\EmailInformationProps;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Components\Pages\Default\DefaultPageProps;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLButtonAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLFormAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputHiddenAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormMethod;
use PeServer\Core\Mvc\Template\Node\Html\Components\BotTextImage\BotTextImage;
use PeServer\Core\Mvc\Template\Node\Html\Components\BotTextImage\BotTextImageProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelper;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperProps;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class AccountSignupStep1Page extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$parameter = $this->props->parameter;
		/** @var AppEmailInformation $email */
		$email = $parameter->values['email'];

		return new DefaultPage(new DefaultPageProps(
			parameter: $parameter,
			title: 'ユーザー登録 1/2',
			children: $this->context->builder->form(
				new HTMLFormAttributes(action: '/account/signup2', method: FormMethod::Post, className: 'page-account-sign-up'),
				new Fragment([
					new Csrf(new CsrfProps(), $this->context),
					$this->context->builder->inputHidden(new HTMLInputHiddenAttributes(
						name: 'account_signup_token',
						value: Text::toString($parameter->values['account_signup_token']),
					)),
					$this->context->builder->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
						$this->context->builder->dt(new HtmlGlobalAttributes(), new TextNode('メールアドレス')),
						$this->context->builder->dd(new HtmlGlobalAttributes(), new InputHelper(new InputHelperProps(
							parameter: $parameter,
							key: 'account_signup_email',
							type: InputHelperType::Email,
							className: 'edit',
							required: true,
						), $this->context)),
						$this->context->builder->dt(new HtmlGlobalAttributes(), new TextNode('認証トークン')),
						$this->context->builder->dd(new HtmlGlobalAttributes(), new Fragment([
							new TextNode('トークン: '),
							new BotTextImage(new BotTextImageProps(
								text: Text::toString($parameter->values['value']),
								alt: 'トークン',
								width: 56,
								height: 28,
								fontSize: 12,
								backgroundColor: '#eeeeeeff',
								foregroundColor: '#0c0c0c',
								className: 'token',
							), $this->context),
							$this->context->builder->br(new HTMLBrAttributes()),
							new TextNode('上記トークンを入力してください。'),
							$this->context->builder->br(new HTMLBrAttributes()),
							new InputHelper(new InputHelperProps(
								parameter: $parameter,
								key: 'account_signup_value',
								type: InputHelperType::Text,
								className: 'edit',
								required: true,
							), $this->context),
						])),
						$this->context->builder->dt(new HtmlGlobalAttributes(className: 'action'), new TextNode('確認')),
						$this->context->builder->dd(new HtmlGlobalAttributes(className: 'action'),
							$this->context->builder->button(new HTMLButtonAttributes(), new TextNode('メール送信'))),
						$this->context->builder->dd(new HtmlGlobalAttributes(),
							new EmailInformation(new EmailInformationProps($email), $this->context)),
					])),
				])
			)
		), $this->context);
	}
}
