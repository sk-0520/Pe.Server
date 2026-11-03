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
use PeServer\Core\Text;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class PasswordResetPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$p = $this->props->parameter;
		/** @var AppEmailInformation $email */
		$email = $p->values['email'];
		$nodes = [];
		foreach ([
			['ログインID', 'reminder_login_id', InputHelperType::Text],
			['新規パスワード', 'reminder_password_new', InputHelperType::Password],
			['新規パスワード(確認用)', 'reminder_password_confirm', InputHelperType::Password],
		] as [$label, $key, $type]) {
			$nodes[] = $b->dt(new HtmlGlobalAttributes(), new TextNode($label));
			$nodes[] = $b->dd(new HtmlGlobalAttributes(), new InputHelper(new InputHelperProps(
				parameter: $p, key: $key, type: $type, className: 'edit', required: true,
			), $this->context));
		}
		$nodes[] = $b->dt(new HtmlGlobalAttributes(className: 'action'), NoneNode::get());
		$nodes[] = $b->dd(new HtmlGlobalAttributes(className: 'action'),
			$b->button(new HTMLButtonAttributes(), new TextNode('再発行')));
		$nodes[] = $b->dd(new HtmlGlobalAttributes(), new EmailInformation(new EmailInformationProps($email), $this->context));
		return new DefaultPage(new DefaultPageProps(
			parameter: $p,
			title: 'パスワード再発行最終工程',
			children: $b->form(
				new HTMLFormAttributes(
					action: '/password/reset/' . Text::toString($p->values['token']),
					method: FormMethod::Post, className: 'page-password-reset',
				),
				new Fragment([
					new Csrf(new CsrfProps(), $this->context),
					$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment($nodes)),
				])
			)
		), $this->context);
	}
}
