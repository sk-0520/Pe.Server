<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Encoding;
use PeServer\Core\Environment;
use PeServer\Core\ErrorHandler;
use PeServer\Core\InitializeChecker;

/**
 * ライブラリ初期化処理。
 */
abstract class CoreInitializer
{
	/**
	 * 初期化チェック
	 */
	private static InitializeChecker $initializeChecker;

	/**
	 * 初期化処理。
	 *
	 * @param string $environment
	 * @return void
	 */
	public static function initialize(string $environment, string $revision): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		Encoding::setDefaultEncoding(Encoding::getUtf8());
		Environment::initialize('C', 'uni', $environment, $revision);

		if(!Environment::isTest()) {
			(new ErrorHandler())->register();
		}
	}
}
