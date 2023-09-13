<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Password;

use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Collections\Arr;
use PeServer\Core\I18n;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;

class PasswordResetLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$this->setValue('token', $this->getRequest('token'));
		$this->setValue('reminder_login_id', $this->getRequest('reminder_login_id'), Text::EMPTY);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->validation('reminder_login_id', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
			// $database = $this->openDatabase();
			// $usersEntityDao = new UsersEntityDao($database);
			// $usersEntityDao->selectExistsLoginId()
		});

		//TODO: AccountUserPasswordLogic と同じなんよ
		$this->validation('reminder_password_new', function (string $key, string $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			$accountValidator->isPassword($key, $value);
		}, ['trim' => false]);

		$this->validation('reminder_password_confirm', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
			$newPassword = $this->getRequest('reminder_password_new', Text::EMPTY, false);
			if ($value !== $newPassword) {
				$this->addError($key, I18n::message('error/password_confirm'));
			}
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}
	}

	#endregion
}
