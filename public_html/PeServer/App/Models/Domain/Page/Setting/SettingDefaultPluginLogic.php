<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\Core\ArrayUtility;
use PeServer\Core\DefaultValue;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\PluginUtility;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginUrlsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginCategoryMappingsEntityDao;

class SettingDefaultPluginLogic extends PageLogicBase
{
	/** @var array{plugin_id:string,plugin_name:string,check_url:string,project_url:string,descriptions:string[],categories:string[],registered:bool}[] */
	private array $defaultPlugins = [
		[
			'plugin_id' => '4524fc23-ebb9-4c79-a26b-8f472c05095e',
			'plugin_name' => 'Pe.Plugins.DefaultTheme',
			'check_url' => '',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'descriptions' => ['本体同梱標準テーマ。', 'ダウンロード先なし。',],
			'categories' => [
				'theme',
			],
			'registered' => false,
		],
		[
			'plugin_id' => '67f0fa7d-52d3-4889-b595-be3703b224eb',
			'plugin_name' => 'Pe.Plugins.Reference.ClassicTheme',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.ClassicTheme.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'descriptions' => ['テーマの参考実装。', 'テーマをプラグインとして扱うのが💩と教えてくれた偉大なる参考実装。',],
			'categories' => [
				'theme',
			],
			'registered' => false,
		],
		[
			'plugin_id' => '2e5c72c5-270f-4b05-afb9-c87f3966ecc5',
			'plugin_name' => 'Pe.Plugins.Reference.Clock',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.Clock.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'descriptions' => ['ランチャーボタン・ウィジェット・設定の参考実装。', '時計を表示する。',],
			'categories' => [
				'utility',
			],
			'registered' => false,
		],
		[
			'plugin_id' => '799ce8bd-8f49-4e8f-9e47-4d4873084081',
			'plugin_name' => 'Pe.Plugins.Reference.Eyes',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.Eyes.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'descriptions' => ['ウィジェット・バックグラウンドの参考実装。', 'xeyes のおめめ。',],
			'categories' => [
				'toy',
			],
			'registered' => false,
		],
		[
			'plugin_id' => '9dcf441d-9f8e-494f-89c1-814678bbc42c',
			'plugin_name' => 'Pe.Plugins.Reference.FileFinder',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.FileFinder.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'descriptions' => ['コマンド入力・設定の参考実装。', 'コマンド入力欄に入力された文字列をファイルパスとして扱う。',],
			'categories' => [
				'file',
				'search',
			],
			'registered' => false,
		],
		[
			'plugin_id' => '4fa1a634-6b32-4762-8ae8-3e1cf6df9db1',
			'plugin_name' => 'Pe.Plugins.Reference.Html',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.Html.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'descriptions' => ['WebView ウィジェットの参考実装。', '常に IME 死んでるマン。',],
			'categories' => [
				'utility',
			],
			'registered' => false,
		],
	];

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$database = $this->openDatabase();

		$pluginsEntityDao = new PluginsEntityDao($database);
		for ($i = 0; $i < ArrayUtility::getCount($this->defaultPlugins); $i++) {
			$this->defaultPlugins[$i]['registered'] = $pluginsEntityDao->selectExistsPluginId($this->defaultPlugins[$i]['plugin_id']);
		}
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			$this->setValue('plugins', $this->defaultPlugins);
			return;
		}

		$account = SessionManager::getAccount();

		if (TypeUtility::parseBoolean($this->getRequest('delete'))) {
			$params = [
				'plugins' => array_filter($this->defaultPlugins, function ($i) {
					return $i['registered'];
				}),
				'user_id' => $account->userId,
			];

			if (ArrayUtility::getCount($params['plugins'])) {
				$database = $this->openDatabase();
				$database->transaction(function (IDatabaseContext $context) use ($params) {

					foreach ($params['plugins'] as $plugin) {
						PluginUtility::removePlugin($context, $plugin['plugin_id']);
						$this->addTemporaryMessage('削除: ' . $plugin['plugin_name']);
					}

					return true;
				});

				AppDatabaseCache::exportPluginInformation();
			} else {
				$this->addTemporaryMessage('なにも削除されず');
			}
		} else {
			$params = [
				'plugins' => array_filter($this->defaultPlugins, function ($i) {
					return !$i['registered'];
				}),
				'user_id' => $account->userId,
			];

			if (ArrayUtility::getCount($params['plugins'])) {
				$database = $this->openDatabase();
				$database->transaction(function (IDatabaseContext $context) use ($params) {
					$pluginsEntityDao = new PluginsEntityDao($context);
					$pluginUrlsEntityDao = new PluginUrlsEntityDao($context);
					$pluginCategoryMappingsEntityDao = new PluginCategoryMappingsEntityDao($context);

					foreach ($params['plugins'] as $plugin) {
						$pluginsEntityDao->insertPlugin(
							$plugin['plugin_id'],
							$params['user_id'],
							$plugin['plugin_name'],
							$plugin['plugin_name'],
							PluginState::ENABLED,
							Text::join("\n\n", $plugin['descriptions']),
							'Pe専用プラグイン'
						);

						$map = [
							PluginUrlKey::CHECK => $plugin['check_url'],
							PluginUrlKey::PROJECT => $plugin['project_url'],
							PluginUrlKey::LANDING => DefaultValue::EMPTY_STRING,
						];
						foreach ($map as $k => $v) {
							$pluginUrlsEntityDao->insertUrl($plugin['plugin_id'], $k, $v);
						}

						foreach ($plugin['categories'] as $categoryId) {
							$pluginCategoryMappingsEntityDao->insertPluginCategoryMapping($plugin['plugin_id'], $categoryId);
						}

						$this->addTemporaryMessage('登録: ' . $plugin['plugin_name']);
					}

					return true;
				});

				AppDatabaseCache::exportPluginInformation();
			} else {
				$this->addTemporaryMessage('なにも登録されず');
			}
		}
	}
}
