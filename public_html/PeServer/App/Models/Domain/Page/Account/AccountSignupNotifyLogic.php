<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\Page\SessionAnonymousTrait;
use PeServer\App\Models\SessionAnonymous;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;

class AccountSignupNotifyLogic extends PageLogicBase
{
	use SessionAnonymousTrait;

	public function __construct(LogicParameter $parameter, private AppConfiguration $config)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->throwHttpStatusIfNotSignup1(HttpStatus::NotFound);
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$emailAddress = $this->config->setting->config->address->fromEmail->address;
		$emailDomain = Text::split($emailAddress, '@')[1];
		$this->setValue('email_address', $emailAddress);
		$this->setValue('email_domain', $emailDomain);
	}

	#endregion
}
