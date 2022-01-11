<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\Core\ArrayUtility;
use PeServer\Core\TypeConverter;
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

class SettingDefaultPluginLogic extends PageLogicBase
{
	/** @var array{plugin_id:string,plugin_name:string,check_url:string,project_url:string,description:string,registered:bool}[] */
	private array $defaultPlugins = [
		[
			'plugin_id' => '4524fc23-ebb9-4c79-a26b-8f472c05095e',
			'plugin_name' => 'Pe.Plugins.DefaultTheme',
			'check_url' => '',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'description' => "本体同梱標準テーマ",
			'registered' => false,
		],
		[
			'plugin_id' => '67f0fa7d-52d3-4889-b595-be3703b224eb',
			'plugin_name' => 'Pe.Plugins.Reference.ClassicTheme',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.ClassicTheme.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'description' => "テーマをプラグインとして扱うのが💩と教えてくれた偉大なる参考実装。\nテーマの参考実装。",
			'registered' => false,
		],
		[
			'plugin_id' => '2e5c72c5-270f-4b05-afb9-c87f3966ecc5',
			'plugin_name' => 'Pe.Plugins.Reference.Clock',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.Clock.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'description' => "時計を表示する。\nウィジェット・設定の参考実装。",
			'registered' => false,
		],
		[
			'plugin_id' => '799ce8bd-8f49-4e8f-9e47-4d4873084081',
			'plugin_name' => 'Pe.Plugins.Reference.Eyes',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.Eyes.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'description' => "xeyes のおめめ。\nウィジェット・バックグラウンドの参考実装。",
			'registered' => false,
		],
		[
			'plugin_id' => '9dcf441d-9f8e-494f-89c1-814678bbc42c',
			'plugin_name' => 'Pe.Plugins.Reference.FileFinder',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.FileFinder.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'description' => "コマンド入力欄に入力された文字列をファイルパスとして扱う。\nコマンドファインダー・設定の参考実装。",
			'registered' => false,
		],
		[
			'plugin_id' => '4fa1a634-6b32-4762-8ae8-3e1cf6df9db1',
			'plugin_name' => 'Pe.Plugins.Reference.Html',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.Html.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe',
			'description' => "常に IME 死んでるマン。\nWebView ウィジェットの参考実装。",
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

		if (TypeConverter::parseBoolean($this->getRequest('delete'))) {
			$params = [
				'plugins' => array_filter($this->defaultPlugins, function ($i) {
					return $i['registered'];
				}),
				'user_id' => $account['user_id'],
			];

			if (ArrayUtility::getCount($params['plugins'])) {
				$database = $this->openDatabase();
				$database->transaction(function (IDatabaseContext $context, $params) {

					foreach ($params['plugins'] as $plugin) {
						PluginUtility::removePlugin($context, $plugin['plugin_id']);
						$this->addTemporaryMessage('削除: ' . $plugin['plugin_name']);
					}

					return true;
				}, $params);

				AppDatabaseCache::exportPluginInformation();
			} else {
				$this->addTemporaryMessage('なにも削除されず');
			}
		} else {
			$params = [
				'plugins' => array_filter($this->defaultPlugins, function ($i) {
					return !$i['registered'];
				}),
				'user_id' => $account['user_id'],
			];

			if (ArrayUtility::getCount($params['plugins'])) {
				$database = $this->openDatabase();
				$database->transaction(function (IDatabaseContext $context, $params) {
					$pluginsEntityDao = new PluginsEntityDao($context);
					$pluginUrlsEntityDao = new PluginUrlsEntityDao($context);

					foreach ($params['plugins'] as $plugin) {
						$pluginsEntityDao->insertPlugin(
							$plugin['plugin_id'],
							$params['user_id'],
							$plugin['plugin_name'],
							$plugin['plugin_name'],
							PluginState::ENABLED,
							$plugin['description'],
							'Pe専用プラグイン'
						);

						$map = [
							PluginUrlKey::CHECK => $plugin['check_url'],
							PluginUrlKey::PROJECT => $plugin['project_url'],
							PluginUrlKey::LANDING => '',
						];
						foreach ($map as $k => $v) {
							$pluginUrlsEntityDao->insertUrl($plugin['plugin_id'], $k, $v);
						}
						$this->addTemporaryMessage('登録: ' . $plugin['plugin_name']);
					}

					return true;
				}, $params);

				AppDatabaseCache::exportPluginInformation();
			} else {
				$this->addTemporaryMessage('なにも登録されず');
			}
		}
	}
}
