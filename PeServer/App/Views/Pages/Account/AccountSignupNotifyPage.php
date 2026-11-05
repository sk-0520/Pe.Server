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
class AccountSignupNotifyPage extends RootHtmlComponentBase
{
	use RootHtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		/** @var AppEmailInformation $email */
		$email = $this->props->parameter->values['email'];

		return new DefaultPage(new DefaultPageProps(
			parameter: $this->props->parameter,
			title: 'ユーザー登録 メール通知',
			children: new Fragment([
				$this->context->builder->p(
                    new HtmlGlobalAttributes(),
					$this->context->builder->strong(
                        new HtmlGlobalAttributes(),
                        new TextNode('アカウント登録用URLを入力されたメールアドレスに送信しました。')
                    )
                ),
				$this->context->builder->p(
                    new HtmlGlobalAttributes(),
                    new TextNode('送信されたアカウント登録用URLは一定時間で無効化されます。')
                ),
				$this->context->builder->hr(new HtmlGlobalAttributes()),
				new EmailInformation(new EmailInformationProps($email), $this->context),
			])
		), $this->context);
	}
}
