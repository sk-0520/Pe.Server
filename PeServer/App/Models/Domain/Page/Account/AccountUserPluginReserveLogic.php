<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\PluginValidator;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\I18n;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Uuid;

class AccountUserPluginReserveLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$keys = [
			'account_plugin_reserve_plugin_id',
			'account_plugin_reserve_plugin_name',
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

		$this->validation('account_plugin_reserve_plugin_id', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator, $this->environment);
			if ($pluginValidator->isPluginId($key, $value)) {
				$database = $this->openDatabase();
				$pluginValidator->isFreePluginId($database, $key, $value);
			}
		});
		$this->validation('account_plugin_reserve_plugin_name', function (string $key, string $value) {
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

		$userInfo = $this->requireSession(SessionKey::ACCOUNT);

		$params = [
			'user_id' => $userInfo->userId,
			'plugin_id' => Uuid::adjustGuid($this->getRequest('account_plugin_reserve_plugin_id')),
			'plugin_name' => $this->getRequest('account_plugin_reserve_plugin_name'),
		];


		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context) use ($params) {
			$pluginsEntityDao = new PluginsEntityDao($context);

			$pluginsEntityDao->insertPlugin(
				$params['plugin_id'],
				$params['user_id'],
				$params['plugin_name'],
				$params['plugin_name'],
				PluginState::RESERVED,
				'',
				Text::EMPTY
			);


			$this->writeAuditLogCurrentUser(AuditLog::USER_PLUGIN_RESERVED, ['plugin_id' => $params['plugin_id'], 'plugin_name' => $params['plugin_name']], $context);

			return true;
		});

		$this->result['plugin_id'] = $params['plugin_id'];
		$this->addTemporaryMessage(I18n::message('message/flash/reserved_plugin'));

		$this->dbCache->exportPluginInformation();
	}

	#endregion
}
