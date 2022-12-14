<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionKey;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\PluginUtility;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginUrlsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginCategoryMappingsEntityDao;
use PeServer\App\Models\Domain\DefaultPlugin;

class ManagementDefaultPluginLogic extends PageLogicBase
{
	/** @var array{item:DefaultPlugin,registered:bool}[] */
	private array $defaultPlugins;

	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);

		$this->defaultPlugins = Arr::map(DefaultPlugin::get(), fn ($i) => [
			'item' => $i,
			'registered' => false,
		]);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$database = $this->openDatabase();

		$pluginsEntityDao = new PluginsEntityDao($database);
		for ($i = 0; $i < Arr::getCount($this->defaultPlugins); $i++) {
			$this->defaultPlugins[$i]['registered'] = $pluginsEntityDao->selectExistsPluginId($this->defaultPlugins[$i]['item']->pluginId);
		}
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			$this->setValue('plugins', $this->defaultPlugins);
			return;
		}

		$account = $this->requireSession(SessionKey::ACCOUNT);

		if (TypeUtility::parseBoolean($this->getRequest('delete'))) {
			$params = [
				'plugins' => array_filter($this->defaultPlugins, function ($i) {
					return $i['registered'];
				}),
				'user_id' => $account->userId,
			];

			if (Arr::getCount($params['plugins'])) {
				$database = $this->openDatabase();
				$database->transaction(function (IDatabaseContext $context) use ($params) {

					foreach ($params['plugins'] as $plugin) {
						/** @var  $plugin array{item:DefaultPlugin,registered:bool}[] */

						PluginUtility::removePlugin($context, $plugin['item']->pluginId);
						$this->addTemporaryMessage('??????: ' . $plugin['item']->pluginName);
					}

					return true;
				});

				$this->dbCache->exportPluginInformation();
			} else {
				$this->addTemporaryMessage('????????????????????????');
			}
		} else {
			$params = [
				'plugins' => array_filter($this->defaultPlugins, function ($i) {
					return !$i['registered'];
				}),
				'user_id' => $account->userId,
			];

			if (Arr::getCount($params['plugins'])) {
				$database = $this->openDatabase();
				$database->transaction(function (IDatabaseContext $context) use ($params) {
					$pluginsEntityDao = new PluginsEntityDao($context);
					$pluginUrlsEntityDao = new PluginUrlsEntityDao($context);
					$pluginCategoryMappingsEntityDao = new PluginCategoryMappingsEntityDao($context);

					foreach ($params['plugins'] as $plugin) {
						/** @var  $plugin array{item:DefaultPlugin,registered:bool}[] */

						$pluginsEntityDao->insertPlugin(
							$plugin['item']->pluginId,
							$params['user_id'],
							$plugin['item']->pluginName,
							$plugin['item']->pluginName,
							PluginState::ENABLED,
							Text::join("\n\n", $plugin['item']->descriptions),
							'Pe?????????????????????'
						);

						$map = [
							PluginUrlKey::CHECK => $plugin['item']->checkUrl,
							PluginUrlKey::PROJECT => $plugin['item']->projectUrl,
							PluginUrlKey::LANDING => Text::EMPTY,
						];
						foreach ($map as $k => $v) {
							$pluginUrlsEntityDao->insertUrl($plugin['item']->pluginId, $k, $v);
						}

						foreach ($plugin['item']->categories as $categoryId) {
							$pluginCategoryMappingsEntityDao->insertPluginCategoryMapping($plugin['item']->pluginId, $categoryId);
						}

						$this->addTemporaryMessage('??????: ' . $plugin['item']->pluginName);
					}

					return true;
				});

				$this->dbCache->exportPluginInformation();
			} else {
				$this->addTemporaryMessage('????????????????????????');
			}
		}
	}
}
