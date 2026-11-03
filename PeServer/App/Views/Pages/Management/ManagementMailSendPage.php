<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use Override;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Input\HTMLInputFileAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\FormEncType;
use PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper\InputHelperType;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessages;
use PeServer\Core\Mvc\Template\Node\Html\Components\ShowErrorMessages\ShowErrorMessagesProps;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\RootProps;

/**
 * @template TRootProps of RootProps
 * @extends ManagementPageBase<TRootProps>
 */
class ManagementMailSendPage extends ManagementPageBase
{
	#[Override]
	protected function getNode(): INode
	{
		$b = $this->context->builder;
		$errors = $this->props->parameter->errors;
		return $this->layout('メール送信', $this->form(
            '/management/mail-send',
			$b->dl(new HtmlGlobalAttributes(className: 'input'), new Fragment([
				$this->input('宛先', 'mail_to', InputHelperType::Email, true),
				$this->input('件名', 'mail_subject', InputHelperType::Text, true),
				$this->input('本文', 'mail_body', InputHelperType::Textarea, true),
				$this->field('添付ファイル', new Fragment([
					$b->inputFile(new HTMLInputFileAttributes(id: 'mail_attachment', name: 'mail_attachment', className: 'edit' . (empty($errors['mail_attachment']) ? '' : ' error'))),
					new ShowErrorMessages(new ShowErrorMessagesProps($errors, 'mail_attachment'), $this->context),
				])),
				$this->submit('submit', '実行'),
			])),
            encType: FormEncType::MultipartFormData
		));
	}
}
