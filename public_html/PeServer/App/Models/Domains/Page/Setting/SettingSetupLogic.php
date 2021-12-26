<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Setting;

use PeServer\Core\I18n;
use PeServer\Core\StringUtility;
use PeServer\Core\Mvc\Validations;
use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\AccountValidator;
use \PeServer\App\Models\Domains\Page\PageLogicBase;

class SettingSetupLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function registerKeysImpl(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'setting_setup_login_id',
			'setting_setup_password',
			'setting_setup_user_name',
			'setting_setup_web_site',
			'setting_setup_mail_address',
		], true);

		$this->setValue('setting_setup_password', '');
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$this->validation('setting_setup_login_id', function ($key, $value) {
			$accountValidator = new AccountValidator($this, $this->validator);
			if ($this->validation->isNotWhiteSpace($key, $value)) {
				$this->validation->inLength($key, 10, $value);
				$this->validation->isMatch($key, '/[a-zA-Z0-9]+/', $value);
			}
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			$this->executeInitialize($callMode);
		} else {
			$this->executeSubmit($callMode);
		}
	}

	private function executeInitialize(LogicCallMode $callMode): void
	{
	}

	private function executeSubmit(LogicCallMode $callMode): void
	{
	}
}
