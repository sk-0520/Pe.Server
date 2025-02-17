<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppEmailInformation;
use PeServer\App\Models\Data\EmailInformation;
use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\Page\SessionAnonymousTrait;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;

class AccountSignupNotifyLogic extends PageLogicBase
{
	use SessionAnonymousTrait;

	public function __construct(LogicParameter $parameter, private AppEmailInformation $appEmailInformation)
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
		$this->setValue('email', $this->appEmailInformation);
	}

	#endregion
}
