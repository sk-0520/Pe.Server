<?php

declare(strict_types=1);

namespace PeServer\Core;

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

		mb_language('ja');
		mb_internal_encoding('UTF-8');

		Environment::initialize($environment, $revision);

		if(!Environment::isTest()) {
			(new ErrorHandler())->register();
		}
	}
}
