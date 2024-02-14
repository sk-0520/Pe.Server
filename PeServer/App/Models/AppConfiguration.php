<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\Configuration\AppSetting;
use PeServer\Core\ArrayUtility;
use PeServer\Core\ProgramContext;
use PeServer\Core\Environment;
use PeServer\Core\I18n;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Web\WebSecurity;
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
	#region variable

	public readonly ProgramContext $context;

	/**
	 * 設定データ。
	 */
	public readonly AppSetting $setting;

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

	#endregion

	/**
	 * 初期化。
	 *
	 * @param ProgramContext $programContext
	 * @param SpecialStore $specialStore
	 */
	public function __construct(ProgramContext $programContext, IUrlHelper $urlHelper, WebSecurity $webSecurity, SpecialStore $specialStore, Environment $environment)
	{
		$this->context = $programContext;

		$this->settingDirectoryPath = Path::combine($this->context->applicationDirectory, 'config');

		$appConfig = self::load($this->settingDirectoryPath, $this->context->rootDirectory, $this->context->applicationDirectory, $this->context->publicDirectory, $environment->get(), 'setting.json');
		$i18nConfig = self::load($this->settingDirectoryPath, $this->context->rootDirectory, $this->context->applicationDirectory, $this->context->publicDirectory, $environment->get(), 'i18n.json');

		$mapper = new Mapper();
		$appSetting = new AppSetting();
		$mapper->mapping($appConfig, $appSetting);

		Directory::setTemporaryDirectory($appSetting->cache->temporary);

		$storeOptions = StoreConfiguration::build($appSetting->store);
		$stores = new Stores($specialStore, $storeOptions, $webSecurity);

		I18n::initialize($i18nConfig);

		$this->setting = $appSetting;
		$this->urlHelper = $urlHelper;
		$this->stores = $stores;
	}

	#region function

	/**
	 * 設定ファイル読み込み。
	 *
	 * @param string $rootDirectoryPath
	 * @param string $publicDirectoryPath
	 * @param string $environment
	 * @return array<mixed>
	 */
	private static function load(string $settingDirectoryPath, string $rootDirectoryPath, string $applicationDirectoryPath, string $publicDirectoryPath, string $environment, string $fileName): array
	{
		$configuration = new Configuration($environment);
		$setting = $configuration->load($settingDirectoryPath, $fileName);

		return $configuration->replace(
			$setting,
			[
				'ROOT' => $rootDirectoryPath,
				'APP' => $applicationDirectoryPath,
				'PUBLIC' => $publicDirectoryPath,
				'ENV' => $environment
			],
			'$(',
			')'
		);
	}

	#endregion
}
