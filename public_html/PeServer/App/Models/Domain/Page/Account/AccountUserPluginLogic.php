<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;
use PeServer\App\Models\Dao\Entities\PluginCategoryMappingsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginUrlsEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\App\Models\Domain\PluginValidator;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\I18n;
use PeServer\Core\Text;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
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
	 * @phpstan-var DatabaseTableResult<array{plugin_category_id:string,display_name:string,description:string}>|null
	 */
	private ?DatabaseTableResult $pluginCategories = null;

	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache, bool $isRegister)
	{
		parent::__construct($parameter);

		$this->isRegister = $isRegister;
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$keys = [
			'account_plugin_plugin_id',
			'account_plugin_plugin_name',
			'account_plugin_display_name',
			'account_plugin_check_url',
			'account_plugin_lp_url',
			'account_plugin_project_url',
			'account_plugin_description',
			'account_plugin_state',
			'plugin_categories',
			'plugin_category_ids',
			'plugin_category_mappings',
		];

		$database = $this->openDatabase();
		$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($database);
		$this->pluginCategories = $pluginCategoriesEntityDao->selectAllPluginCategories();

		foreach ($this->pluginCategories->rows as $category) {
			$keys[] = 'plugin_category_' . $category['plugin_category_id'];
		}

		if (!$this->isRegister) {
			$keys = array_merge($keys, [
				'from_account_plugin_plugin_id',
			]);
		}

		$this->registerParameterKeys($keys, true);

		if (!$this->isRegister) {
			$pluginId = Uuid::adjustGuid($this->getRequest('plugin_id'));

			if ($callMode->isInitialize()) {
				$this->setValue('account_plugin_plugin_id', $pluginId);
				$this->setValue('from_account_plugin_plugin_id', $pluginId);
			} else {
				$fromPluginId = $this->getRequest('from_account_plugin_plugin_id');
				// プラグインIDが変更されているので死んでもらう(二つとも合うように加工された場合はもう無理、要求を粛々と処理する)
				if (!Uuid::isEqualGuid($pluginId, $fromPluginId)) {
					throw new HttpStatusException(HttpStatus::badRequest());
				}
				// プラグインIDとプラグイン名は原本を使用する
				$database = $this->openDatabase();
				$pluginsEntityDao = new PluginsEntityDao($database);
				// ルーティングでこのプラグイン所有者が保証されているのでプラグインIDのみで取得
				$map = $pluginsEntityDao->selectPluginIds($pluginId);
				$this->setValue('account_plugin_plugin_id', $map->fields['plugin_id']);
				$this->setValue('account_plugin_plugin_name', $map->fields['plugin_name']);
				$this->setValue('account_plugin_state', $map->fields['plugin_name']);
			}
		} else {
			$this->setValue('account_plugin_state', Text::EMPTY);
			$this->setValue('plugin_category_mappings', []);
		}
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
				$pluginId = $this->getRequest('plugin_id');

				$database = $this->openDatabase();
				$pluginsEntityDao = new PluginsEntityDao($database);
				$pluginUrlsEntityDao = new PluginUrlsEntityDao($database);
				$pluginCategoryMappingsEntityDao = new PluginCategoryMappingsEntityDao($database);

				// ルーティングでこのプラグイン所有者が保証されているのでプラグインIDのみで取得
				$editMap = $pluginsEntityDao->selectEditPlugin($pluginId);
				$this->setValue('account_plugin_plugin_name', $editMap->fields['plugin_name']);
				$this->setValue('account_plugin_display_name', $editMap->fields['display_name']);
				$this->setValue('account_plugin_description', $editMap->fields['description']);

				$urlMap = $pluginUrlsEntityDao->selectUrls($pluginId);
				$this->setValue('account_plugin_check_url', Arr::getOr($urlMap, PluginUrlKey::CHECK, Text::EMPTY));
				$this->setValue('account_plugin_lp_url', Arr::getOr($urlMap, PluginUrlKey::LANDING, Text::EMPTY));
				$this->setValue('account_plugin_project_url', Arr::getOr($urlMap, PluginUrlKey::PROJECT, Text::EMPTY));

				$pluginCategoryMappings = $pluginCategoryMappingsEntityDao->selectPluginCategoriesByPluginId($pluginId);
				$this->setValue('plugin_category_mappings', $pluginCategoryMappings);
			}

			return;
		}

		$userInfo = $this->requireSession(SessionKey::ACCOUNT);

		$params = [
			'user_id' => $userInfo->userId,
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
			$params['plugin_id'] = Uuid::adjustGuid($this->getRequest('plugin_id'));
		}

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context) use ($params) {
			assert($this->pluginCategories !== null);

			$pluginsEntityDao = new PluginsEntityDao($context);
			$pluginUrlsEntityDao = new PluginUrlsEntityDao($context);
			$pluginCategoryMappingsEntityDao = new PluginCategoryMappingsEntityDao($context);

			$pluginCategories = [];
			foreach ($this->pluginCategories->rows as $category) {
				if (TypeUtility::parseBoolean($this->getRequest('plugin_category_' . $category['plugin_category_id']))) {
					$pluginCategories[] = $category['plugin_category_id'];
				}
			}

			if ($this->isRegister) {
				$pluginsEntityDao->insertPlugin(
					$params['plugin_id'],
					$params['user_id'],
					$params['plugin_name'], //@phpstan-ignore-line
					$params['display_name'],
					PluginState::ENABLED,
					$params['description'],
					Text::EMPTY
				);
			} else {
				$pluginsEntityDao->updateEditPlugin(
					$params['plugin_id'],
					$params['user_id'],
					$params['display_name'],
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
			foreach ($this->pluginCategories->rows as $pluginCategory) {
				$pluginCategoryId = $pluginCategory['plugin_category_id'];
				if (TypeUtility::parseBoolean($this->getRequest('plugin_category_' . $pluginCategoryId))) {
					$pluginCategoryMappingsEntityDao->insertPluginCategoryMapping($params['plugin_id'], $pluginCategoryId);
				}
			}

			if ($this->isRegister) {
				//@phpstan-ignore-next-line
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
		assert($this->pluginCategories !== null);

		$this->setValue('plugin_categories', $this->pluginCategories->rows);

		$pluginCategoryIds = array_map(function ($i) {
			return $i['plugin_category_id'];
		}, $this->pluginCategories->rows); //@phpstan-ignore-line not null
		$this->setValue('plugin_category_ids', $pluginCategoryIds);

		if ($callMode->isSubmit()) {
			$pluginCategories = [];
			foreach ($pluginCategoryIds as $pluginCategoryId) {
				if (TypeUtility::parseBoolean($this->getRequest('plugin_category_' . $pluginCategoryId))) {
					$pluginCategories[] = $pluginCategoryId;
				}
			}

			$this->setValue('plugin_category_mappings', $pluginCategories);
		}
	}
}
