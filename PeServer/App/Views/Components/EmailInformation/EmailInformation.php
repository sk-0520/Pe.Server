<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\EmailInformation;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\TextNode;

/**
 * @extends HtmlComponentBase<EmailInformationProps>
 */
class EmailInformation extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<EmailInformationProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		return $this->context->builder->section(
			new HtmlGlobalAttributes(className: 'email-information'),
			new Fragment([
				$this->context->builder->h6(new HtmlGlobalAttributes(), new TextNode('📨メール送信について')),
				$this->context->builder->p(new HtmlGlobalAttributes(), new Fragment([
					new TextNode('受信拒否をしている場合、'),
					$this->context->builder->code(
						new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'inline'])),
						new TextNode($this->props->email->address)
					),
					new TextNode('を許可してください。'),
				])),
				$this->context->builder->p(new HtmlGlobalAttributes(), new Fragment([
					new TextNode('その他通知メールを送信する可能性もあるのでドメイン'),
					$this->context->builder->code(
						new HtmlGlobalAttributes(data: new DataAttributes(['clipboard' => 'inline'])),
						new TextNode($this->props->email->domain)
					),
					new TextNode('を許可してもらえるとうれしいです。'),
				])),
			])
		);
	}
}
