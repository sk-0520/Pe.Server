<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\StringUtility;

class AccountSignupNotifyLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$emailAddress = AppConfiguration::$config['config']['address']['from_email']['address'];
		$emailDomain = StringUtility::split($emailAddress, '@')[1];
		$this->setValue('email_address', $emailAddress);
		$this->setValue('email_domain', $emailDomain);
	}
}
