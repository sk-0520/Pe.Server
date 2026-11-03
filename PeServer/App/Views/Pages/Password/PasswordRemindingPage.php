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
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\RootHtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @template TRootProps of RootProps
 * @extends RootHtmlComponentBase<TRootProps>
 */
class PasswordRemindingPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		/** @var AppEmailInformation $email */
		$email = $this->props->parameter->values['email'];
		$b = $this->context->builder;
		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: 'パスワード再発行メール送信',
			children: new Fragment([
				$b->p(new HtmlGlobalAttributes(), new TextNode('パスワード再発行メールを送信しました。')),
				$b->p(new HtmlGlobalAttributes(), new TextNode('次工程はメールに記載されているURLを確認してください。')),
				new EmailInformation(new EmailInformationProps($email), $this->context),
			])
		), $this->context);
	}
}
