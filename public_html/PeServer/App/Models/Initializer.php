<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\CoreInitializer;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Store\SpecialStore;
use PeServer\App\Models\AppConfiguration;

abstract class Initializer
{
	/**
	 * 初期化チェック
	 */
	private static InitializeChecker $initializeChecker;

	/**
	 * 初期化。
	 *
	 * @param string $rootDirectoryPath 公開ルートディレクトリ
	 * @param string $baseDirectoryPath `\PeServer\*` のルートディレクトリ
	 * @param SpecialStore $specialStore
	 * @param string $environment
	 * @param string $revision
	 */
	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $urlBasePath, SpecialStore $specialStore, string $environment, string $revision): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		CoreInitializer::initialize($environment, $revision);
		AppConfiguration::initialize($rootDirectoryPath, $baseDirectoryPath, $urlBasePath, $specialStore);
	}
}
