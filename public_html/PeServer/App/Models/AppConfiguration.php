<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\Configuration\AppSetting;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Environment;
use PeServer\Core\I18n;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;
use PeServer\Core\Serialization\Configuration;
use PeServer\Core\Serialization\Mapper;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;


/**
 * アプリ設定。
 */
class AppConfiguration
{
	/**
	 * 設定データ。
	 *
	 * @var AppSetting
	 * @readonly
	 */
	public AppSetting $setting;

	/**
	 * ルートディレクトリ。
	 *
	 * @var string
	 */
	public string $rootDirectoryPath;
	/**
	 * ベースディレクトリ。
	 *
	 * 基本的にこちらを使っておけば問題なし。
	 *
	 * @var string
	 */
	public string $baseDirectoryPath;

	/**
	 * URL ベースパス。
	 *
	 * @var IUrlHelper
	 */
	public IUrlHelper $urlHelper;

	/**
	 * 設定ファイル置き場。
	 *
	 * @var string
	 */
	public string $settingDirectoryPath;

	public Stores $stores;

	/**
	 * 初期化。
	 *
	 * @param string $rootDirectoryPath 公開ルートディレクトリ
	 * @param string $baseDirectoryPath `\PeServer\*` のルートディレクトリ
	 * @param SpecialStore $specialStore
	 */
	public function __construct(string $rootDirectoryPath, string $baseDirectoryPath, IUrlHelper $urlHelper, SpecialStore $specialStore)
	{
		$this->settingDirectoryPath = Path::combine($baseDirectoryPath, 'config');

		$tempDirectoryPath = Path::combine($baseDirectoryPath, 'data/temp/buckets');
		Directory::setTemporaryDirectory($tempDirectoryPath);

		$appConfig = $this->load($rootDirectoryPath, $baseDirectoryPath, Environment::get(), 'setting.json');
		$i18nConfig = $this->load($rootDirectoryPath, $baseDirectoryPath, Environment::get(), 'i18n.json');

		$mapper = new Mapper();
		$appSetting = new AppSetting();
		$mapper->mapping($appConfig, $appSetting);

		$storeOptions = StoreConfiguration::build($appSetting->store);
		$stores = new Stores($specialStore, $storeOptions);

		I18n::initialize($i18nConfig);

		$this->setting = $appSetting;
		$this->rootDirectoryPath = $rootDirectoryPath;
		$this->baseDirectoryPath = $baseDirectoryPath;
		$this->urlHelper = $urlHelper;
		$this->stores = $stores;
	}

	/**
	 * 設定ファイル読み込み。
	 *
	 * @param string $rootDirectoryPath
	 * @param string $baseDirectoryPath
	 * @param string $environment
	 * @return array<mixed>
	 */
	private function load(string $rootDirectoryPath, string $baseDirectoryPath, string $environment, string $fileName): array
	{
		$configuration = new Configuration($environment);
		$setting = $configuration->load($this->settingDirectoryPath, $fileName);

		return $configuration->replace(
			$setting,
			[
				'ROOT' => $rootDirectoryPath,
				'BASE' => $baseDirectoryPath,
				'ENV' => $environment
			],
			'$(',
			')'
		);
	}
}
