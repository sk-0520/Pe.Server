<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\PluginValidator;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;

class AccountUserPluginTemporaryLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$keys = [
			'account_temporary_plugin_plugin_id',
			'account_temporary_plugin_plugin_name',
		];

		$this->registerParameterKeys($keys, true);

		if ($callMode === LogicCallMode::Initialize) {
			foreach ($keys as $key) {
				$this->setValue($key, Text::EMPTY);
			}
		}
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->validation('account_plugin_plugin_id', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator, $this->environment);
			if ($pluginValidator->isPluginId($key, $value)) {
				$database = $this->openDatabase();
				$pluginValidator->isFreePluginId($database, $key, $value);
			}
		});
		$this->validation('account_plugin_plugin_name', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator, $this->environment);
			if ($pluginValidator->isPluginName($key, $value)) {
				$database = $this->openDatabase();
				$pluginValidator->isFreePluginName($database, $key, $value);
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
