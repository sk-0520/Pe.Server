<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;
use PeServer\App\Models\Dao\Entities\PluginCategoryMappingsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginUrlsEntityDao;
use PeServer\App\Models\Data\Dto\PluginCategoryDto;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\App\Models\Domain\PluginValidator;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\I18n;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\TypeUtility;
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

	/**
	 * プラグインカテゴリ一覧。
	 *
	 * @var PluginCategoryDto[]
	 */
	private array $pluginCategories = [];

	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache, bool $isRegister)
	{
		parent::__construct($parameter);

		$this->isRegister = $isRegister;
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$keys = [
			'account_plugin_plugin_id',
			'account_plugin_plugin_name',
			'account_plugin_display_name',
			'account_plugin_state',
			'account_plugin_check_url',
			'account_plugin_lp_url',
			'account_plugin_project_url',
			'account_plugin_description',
			'plugin_categories',
			'plugin_category_ids',
			'plugin_category_mappings',
			'plugin_state_items',
			'plugin_state_editable_items',
		];

		$database = $this->openDatabase();
		$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($database);
		$this->pluginCategories = $pluginCategoriesEntityDao->selectAllPluginCategories();

		foreach ($this->pluginCategories as $category) {
			$keys[] = 'plugin_category_' . $category->pluginCategoryId;
		}

		if (!$this->isRegister) {
			$keys = array_merge($keys, [
				'from_account_plugin_plugin_id',
			]);
		}

		$this->registerParameterKeys($keys, true);


		if (!$this->isRegister) {
			$pluginId = Uuid::adjustGuid($this->getRequest('plugin_id'));
			if ($callMode === LogicCallMode::Initialize) {
				$this->setValue('account_plugin_plugin_id', $pluginId);
				$this->setValue('from_account_plugin_plugin_id', $pluginId);
			} else {
				$fromPluginId = $this->getRequest('from_account_plugin_plugin_id');
				// プラグインIDが変更されているので死んでもらう(二つとも合うように加工された場合はもう無理、要求を粛々と処理する)
				if (!Uuid::isEqualGuid($pluginId, $fromPluginId)) {
					throw new HttpStatusException(HttpStatus::BadRequest);
				}
				// プラグインIDとプラグイン名は原本を使用する
				$database = $this->openDatabase();
				$pluginsEntityDao = new PluginsEntityDao($database);
				// ルーティングでこのプラグイン所有者が保証されているのでプラグインIDのみで取得
				$map = $pluginsEntityDao->selectPluginIds($pluginId);
				$this->setValue('account_plugin_plugin_id', $map->fields['plugin_id']);
				$this->setValue('account_plugin_plugin_name', $map->fields['plugin_name']);
				$this->setValue('account_plugin_state', $map->fields['state']);
			}
		} else {
			$this->setValue('plugin_category_mappings', []);
		}
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$pluginId = Uuid::adjustGuid($this->getRequest('plugin_id'));

		if ($this->isRegister) {
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

		$this->validation('account_plugin_plugin_id', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator, $this->environment);
			if ($pluginValidator->isPluginName($key, $value)) {
				$database = $this->openDatabase();
				$pluginValidator->isFreePluginName($database, $key, $value);
			}
		});


		$this->validation('account_plugin_display_name', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator, $this->environment);
			$pluginValidator->isDisplayName($key, $value);
		});

		$this->validation('account_plugin_check_url', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator, $this->environment);
			$pluginValidator->isCheckUrl($key, $value);
		});

		$this->validation('account_plugin_lp_url', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator, $this->environment);
			$pluginValidator->isWebsite($key, $value);
		});

		$this->validation('account_plugin_project_url', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator, $this->environment);
			$pluginValidator->isWebsite($key, $value);
		});

		$this->validation('account_plugin_description', function (string $key, string $value) {
			$pluginValidator = new PluginValidator($this, $this->validator, $this->environment);
			$pluginValidator->isDescription($key, $value);
		});

		if (!$this->isRegister) {
			$this->validation('account_plugin_state', function (string $key, string $value) use ($pluginId) {
				$database = $this->openDatabase();
				$pluginsEntityDao = new PluginsEntityDao($database);

				$currentPlugin = $pluginsEntityDao->selectEditPlugin($pluginId);
				$currentState = $currentPlugin->fields['state'];
			});
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			if (!$this->isRegister) {
				// 既存データを引っ張ってくる
				$pluginId = $this->getRequest('plugin_id');

				$database = $this->openDatabase();
				$pluginsEntityDao = new PluginsEntityDao($database);
				$pluginUrlsEntityDao = new PluginUrlsEntityDao($database);
				$pluginCategoryMappingsEntityDao = new PluginCategoryMappingsEntityDao($database);

				// ルーティングでこのプラグイン所有者が保証されているのでプラグインIDのみで取得
				$editMap = $pluginsEntityDao->selectEditPlugin($pluginId);
				$this->setValue('account_plugin_plugin_name', $editMap->fields['plugin_name']);
				$this->setValue('account_plugin_display_name', $editMap->fields['display_name']);
				$this->setValue('account_plugin_state', $editMap->fields['state']);
				$this->setValue('account_plugin_description', $editMap->fields['description']);

				$urlMap = $pluginUrlsEntityDao->selectUrls($pluginId);
				$this->setValue('account_plugin_check_url', $urlMap[PluginUrlKey::CHECK] ?? Text::EMPTY);
				$this->setValue('account_plugin_lp_url', $urlMap[PluginUrlKey::LANDING] ?? Text::EMPTY);
				$this->setValue('account_plugin_project_url', $urlMap[PluginUrlKey::PROJECT] ?? Text::EMPTY);

				$pluginCategoryMappings = $pluginCategoryMappingsEntityDao->selectPluginCategoriesByPluginId($pluginId);
				$this->setValue('plugin_category_mappings', $pluginCategoryMappings);
			} else {
				$this->setValue('account_plugin_state', PluginState::DISABLED);
			}

			return;
		}

		$userInfo = $this->requireSession(SessionKey::ACCOUNT);

		$params = [
			'plugin_id' => $this->isRegister
				? Uuid::adjustGuid($this->getRequest('account_plugin_plugin_id'))
				: Uuid::adjustGuid($this->getRequest('plugin_id'))
			,
			'plugin_name' => $this->isRegister
				? $this->getRequest('account_plugin_plugin_name')
				: Text::EMPTY
			,
			'user_id' => $userInfo->userId,
			'display_name' => $this->getRequest('account_plugin_display_name'),
			'state' => $this->getRequest('account_plugin_state'),
			'urls' => [
				PluginUrlKey::CHECK => $this->getRequest('account_plugin_check_url'),
				PluginUrlKey::LANDING => $this->getRequest('account_plugin_lp_url'),
				PluginUrlKey::PROJECT => $this->getRequest('account_plugin_project_url'),
			],
			'description' => $this->getRequest('account_plugin_description'),
		];

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context) use ($params) {
			$pluginsEntityDao = new PluginsEntityDao($context);
			$pluginUrlsEntityDao = new PluginUrlsEntityDao($context);
			$pluginCategoryMappingsEntityDao = new PluginCategoryMappingsEntityDao($context);

			$pluginState = $params['state'];
			if (!$this->isRegister) {
				$currentPlugin = $pluginsEntityDao->selectEditPlugin($params['plugin_id']);
				if (Text::isNullOrEmpty($params['state'])) {
					$pluginState = $currentPlugin->fields['state'];
				}
			}


			$pluginCategories = [];
			foreach ($this->pluginCategories as $category) {
				if (TypeUtility::parseBoolean($this->getRequest('plugin_category_' . $category->pluginCategoryId))) {
					$pluginCategories[] = $category->pluginCategoryId;
				}
			}

			if ($this->isRegister) {
				$pluginsEntityDao->insertPlugin(
					$params['plugin_id'],
					$params['user_id'],
					$params['plugin_name'],
					$params['display_name'],
					$pluginState,
					$params['description'],
					Text::EMPTY
				);
			} else {
				$pluginsEntityDao->updateEditPlugin(
					$params['plugin_id'],
					$params['user_id'],
					$params['display_name'],
					$pluginState,
					$params['description']
				);
			}

			$pluginUrlsEntityDao->deleteByPluginId($params['plugin_id']);
			/** @var array<string,string> */
			$urls = $params['urls'];
			foreach ($urls as $k => $v) {
				$pluginUrlsEntityDao->insertUrl($params['plugin_id'], $k, $v);
			}

			$pluginCategoryMappingsEntityDao->deletePluginCategoryMappings($params['plugin_id']);
			foreach ($this->pluginCategories as $pluginCategory) {
				if (TypeUtility::parseBoolean($this->getRequest('plugin_category_' . $pluginCategory->pluginCategoryId))) {
					$pluginCategoryMappingsEntityDao->insertPluginCategoryMapping($params['plugin_id'], $pluginCategory->pluginCategoryId);
				}
			}

			if ($this->isRegister) {
				$this->writeAuditLogCurrentUser(AuditLog::USER_PLUGIN_REGISTER, ['plugin_id' => $params['plugin_id'], 'plugin_name' => $params['plugin_name']], $context);
			} else {
				$this->writeAuditLogCurrentUser(AuditLog::USER_PLUGIN_UPDATE, ['plugin_id' => $params['plugin_id']], $context);
			}

			return true;
		});

		$this->result['plugin_id'] = $params['plugin_id'];
		if ($this->isRegister) {
			$this->addTemporaryMessage(I18n::message('message/flash/register_plugin'));
		} else {
			$this->addTemporaryMessage(I18n::message('message/flash/update_plugin'));
		}
		$this->dbCache->exportPluginInformation();
	}

	protected function cleanup(LogicCallMode $callMode): void
	{
		$this->setValue('plugin_categories', $this->pluginCategories);
		$this->setValue(
			'plugin_state_items',
			$this->isRegister ? PluginState::getEditableItems() : PluginState::getItems()
		);
		$this->setValue('plugin_state_editable_items', PluginState::getEditableItems());

		$pluginCategoryIds = array_map(function ($i) {
			return $i->pluginCategoryId;
		}, $this->pluginCategories);
		$this->setValue('plugin_category_ids', $pluginCategoryIds);

		if ($callMode === LogicCallMode::Submit) {
			$pluginCategories = [];
			foreach ($pluginCategoryIds as $pluginCategoryId) {
				if (TypeUtility::parseBoolean($this->getRequest('plugin_category_' . $pluginCategoryId))) {
					$pluginCategories[] = $pluginCategoryId;
				}
			}

			$this->setValue('plugin_category_mappings', $pluginCategories);
		}
	}

	#endregion
}
