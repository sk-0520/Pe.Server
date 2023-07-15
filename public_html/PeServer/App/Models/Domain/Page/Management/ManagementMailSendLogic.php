<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\DI\Inject;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\IO\File;
use PeServer\Core\Mail\Attachment;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class ManagementMailSendLogic extends PageLogicBase
{
	#[Inject] //@phpstan-ignore-next-line Inject
	private HttpRequest $request;

	public function __construct(LogicParameter $parameter, private AppConfiguration $config, private AppMailer $mailer)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'mail_subject',
			'mail_to',
			'mail_body',
		], true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->validation('mail_subject', function(string $key, string $value) {
			$this->validator->isNotEmpty($key, $value);
		});

		$this->validation('mail_to', function(string $key, string $value) {
			$this->validator->isEmail($key, $value);
		});

		$this->validation('mail_body', function(string $key, string $value) {
			$this->validator->isNotEmpty($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if($callMode === LogicCallMode::Initialize) {
			return;
		}

		$mailSubject =$this->getRequest('mail_subject');
		$mailTo =$this->getRequest('mail_to');
		$mailBody =$this->getRequest('mail_body');

		$this->mailer->subject = $mailSubject;

		$this->mailer->toAddresses = [
			new EmailAddress($mailTo),
		];

		$this->mailer->setMessage(new EmailMessage(
			text: $mailBody
		));

		if($this->request->exists('mail_attachment', true)) {
			$file = $this->getFile('mail_attachment');
			$content = File::readContent($file->uploadedFilePath);
			$this->mailer->attachments = [
				new Attachment(
					$file->originalFileName,
					$content,
					$file->mime
				)
			];
		}

		$this->mailer->send();
	}
}
