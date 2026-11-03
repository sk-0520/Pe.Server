<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Password;

use Override;
use PeServer\App\Models\AppEmailInformation;
use PeServer\App\Views\Components\EmailInformation\EmailInformation;
use PeServer\App\Views\Components\EmailInformation\EmailInformationProps;
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

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class PasswordReminderPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$p = $this->props->parameter;
		/** @var AppEmailInformation $email */
		$email = $p->values['email'];

		return new DefaultPage(new DefaultPageProps(
			parameter: $p,
			title: 'パスワード再設定',
			children: $b->form(
				new HTMLFormAttributes(action: '/password2/reminder', method: FormMethod::Post, className: 'page-password-reminder'),
				new Fragment([
					new Csrf(new CsrfProps(), $this->context),
					$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
						$b->dt(new HtmlGlobalAttributes(), new TextNode('ログインID')),
						$b->dd(new HtmlGlobalAttributes(), new InputHelper(new InputHelperProps(
							parameter: $p, key: 'reminder_login_id', type: InputHelperType::Text,
							className: 'edit', required: true,
						), $this->context)),
						$b->dt(new HtmlGlobalAttributes(className: 'action'), NoneNode::get()),
						$b->dd(new HtmlGlobalAttributes(className: 'action'),
							$b->button(new HTMLButtonAttributes(), new TextNode('再発行'))),
						$b->dd(new HtmlGlobalAttributes(), new EmailInformation(new EmailInformationProps($email), $this->context)),
					])),
				])
			)
		), $this->context);
	}
}
