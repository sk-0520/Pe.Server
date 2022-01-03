<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Database\Entities\PluginsEntityDao;
use PeServer\App\Models\Database\Entities\PluginUrlsEntityDao;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\App\Models\Domains\PluginState;
use PeServer\App\Models\Domains\PluginUrlKey;
use PeServer\App\Models\Domains\PluginValidator;
use PeServer\Core\Database;
use PeServer\Core\Uuid;

class AccountUserPluginLogic extends PageLogicBase
{
	/**
	 * 新規作成か。
	 *
	 * 編集時は偽になる。
	 *
	 * @var boolean
	 */
	private bool $isRegister;

	public function __construct(LogicParameter $parameter, bool $isRegister)
	{
		parent::__construct($parameter);

		$this->isRegister = $isRegister;
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$keys = [
			'account_plugin_display_name',
			'account_plugin_check_url',
			'account_plugin_lp_url',
			'account_plugin_project_url',
			'account_plugin_description',
		];

		if ($this->isRegister) {
			$keys = array_merge($keys, [
				'account_plugin_plugin_id',
				'account_plugin_plugin_name',
			]);
		}

		$this->registerParameterKeys($keys, true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		if ($this->isRegister) {
			$this->validation('account_plugin_plugin_id', function (string $key, string $value) {
				$pluginValidator = new PluginValidator($this, $this->validator);
				if ($pluginValidator->isPluginId($key, $value)) {
					$database = $this->openDatabase();
					$pluginValidator->isFreePluginId($database, $key, $value);
				}
			});
			$this->validation('account_plugin_plugin_name', function (string $key, string $value) {
				$pluginValidator = new PluginValidator($this, $this->validator);
				if ($pluginValidator->isPluginName($key, $value)) {
					$database = $this->openDatabase();
					$pluginValidator->isFreePluginName($database, $key, $value);
				}
			});
		}

		$this->validation('account_plugin_display_name', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator);
			$pluginValidator->isDisplayName($key, $value);
		});

		$this->validation('account_plugin_check_url', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator);
			$pluginValidator->isCheckUrl($key, $value);
		});

		$this->validation('account_plugin_lp_url', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator);
			$pluginValidator->isWebsite($key, $value);
		});

		$this->validation('account_plugin_project_url', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator);
			$pluginValidator->isWebsite($key, $value);
		});

		$this->validation('account_plugin_description', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator);
			$pluginValidator->isDescription($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			if (!$this->isRegister) {
				// 既存データを引っ張ってくる
			}

			return;
		}

		$userInfo = $this->userInfo();

		$params = [
			'user_id' => $userInfo['user_id'],
			'display_name' => $this->getRequest('account_plugin_display_name'),
			'urls' => [
				PluginUrlKey::CHECK => $this->getRequest('account_plugin_check_url'),
				PluginUrlKey::LANDING => $this->getRequest('account_plugin_lp_url'),
				PluginUrlKey::PROJECT => $this->getRequest('account_plugin_project_url'),
			],
			'description' => $this->getRequest('account_plugin_description'),
		];

		if ($this->isRegister) {
			$params['plugin_id'] = Uuid::adjustGuid($this->getRequest('account_plugin_plugin_id'));
			$params['plugin_name'] = $this->getRequest('account_plugin_plugin_name');
		} else {
			$params['plugin_id'] = Uuid::adjustGuid($this->getRequest('account_plugin_plugin_id'));
		}

		$database = $this->openDatabase();
		$database->transaction(function (Database $database, $params) {
			$pluginsEntityDao = new PluginsEntityDao($database);
			$pluginUrlsEntityDao = new PluginUrlsEntityDao($database);

			if ($this->isRegister) {
				$pluginsEntityDao->insertPlugin(
					$params['plugin_id'],
					$params['user_id'],
					$params['plugin_name'],
					$params['display_name'],
					PluginState::ENABLED,
					$params['description'],
					''
				);
			} else {
				$pluginsEntityDao->updatePluginEdit(
					$params['plugin_id'],
					$params['user_id'],
					$params['display_name'],
					$params['description']
				);
			}

			$pluginUrlsEntityDao->deleteByPluginId($params['plugin_id']);
			foreach ($params['urls'] as $k => $v) {
				$pluginUrlsEntityDao->insertUrl($params['plugin_id'], $k, $v);
			}

			if ($this->isRegister) {
				$this->writeAuditLogCurrentUser(AuditLog::USER_PLUGIN_REGISTER, ['plugin_id' => $params['plugin_id'], 'plugin_name' => $params['plugin_name']], $database);
			} else {
				$this->writeAuditLogCurrentUser(AuditLog::USER_PLUGIN_UPDATE, ['plugin_id' => $params['plugin_id']], $database);
			}

			return true;
		}, $params);

		$this->result['plugin_id'] = $params['plugin_id'];
	}
}
