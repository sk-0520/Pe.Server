<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Password;

use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\AppEmailInformation;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\Page\SessionAnonymousTrait;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Text;

class PasswordRemindingLogic extends PageLogicBase
{
	use SessionAnonymousTrait;

	public function __construct(LogicParameter $parameter, private AppEmailInformation $appEmailInformation)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->throwHttpStatusIfNotPasswordReminder(HttpStatus::NotFound);
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		//TODO: AccountSignupNotifyLogic と同じ処理すべきじゃないかなぁ
		$this->setValue('email', $this->appEmailInformation);
	}

	#endregion
}
